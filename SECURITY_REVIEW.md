# Oxygen Laravel Package — Security Review

Thoroughness: very thorough. Focus: injection, file handling, deserialization, SSRF, path traversal, command injection, XSS with real impact, crypto/secrets, infrastructure.

Date: 2026-11-23  
Branch: `cursor/vulnerability-findings-memory-b634`

## Validated findings (MEDIUM / HIGH / CRITICAL)

### 1. Lower-privileged admin can reset Super-Admin passwords (account takeover)

- **Severity:** CRITICAL
- **Primary location:** `src/Http/Controllers/Auth/UsersController.php`
- **Description:** `updatePassword` allows any authenticated user who can reach the manage-users routes (`roles[super-admins|admins|developers]`) to set a new password for **any** user ID. It only verifies the *acting* admin’s current password, then overwrites the *target* user’s password. There is no check that the actor may manage that account, and no role-hierarchy check preventing an `admins` user from resetting a `super-admins` user.
- **Impact:** Full account takeover of Super-Admin (and any other user), privilege escalation from client Admin → Super-Admin.
- **Attack path:**
  1. Authenticate as a user in the `admins` role (seeded with user-management abilities; route ACL also allows this role into `/account/users/*`).
  2. Open `/account/users/{superAdminId}/edit-password` (UI exposes “Edit Password” for every non-self user with no hierarchy gate).
  3. Submit actor’s current password + a new password for the Super-Admin.
  4. Log in as the Super-Admin with the new password (target remember token and device tokens are cleared).
- **Evidence:**
  - `src/Http/Controllers/Auth/UsersController.php:156-190` — no authorization beyond “user exists”; sets `$user->password` for arbitrary `$id`
  - `publish/routes/oxygen-web.php:133,194-201` — users routes only gated by `auth` + role group including `admins`
  - `resources/views/manage/users/index.blade.php:54-55` — Edit Password link shown for all other users
  - `publish/database/seeders/Auth/PermissionsTableSeeder.php:33-35` — `admins` is intentionally less privileged than `super-admins`
- **Remediation:** Enforce server-side authorization (e.g. `edit-users` / dedicated permission) and a role hierarchy: actors must not change passwords for users with equal/higher privilege roles. Prefer requiring a confirmation challenge and auditing. Mirror the same guards on `update`, `updateDisabled`, and `destroy`.

---

### 2. Invitation claim does not bind registrant email to invite email (privilege theft)

- **Severity:** CRITICAL
- **Primary location:** `src/Http/Controllers/Auth/RegisteredUserController.php`
- **Description:** Visiting `/invitations/join/{code}` stores `invitation_code` in the session. Registration then attaches `invite->role_id` to the newly created user **without** verifying that the submitted email matches `invite->email`. The register form only prefills the email; it remains attacker-editable. Deprecated `LoginController::authenticated` has the same missing bind for login-based claims (if still wired).
- **Impact:** Anyone who obtains a privileged invitation link (email forward, mailbox access, leaked URL) can register an arbitrary attacker-controlled account and inherit Super-Admin / Developer / Admin roles.
- **Attack path:**
  1. Obtain invite URL for a high-privilege role (e.g. Super-Admins).
  2. Open `/invitations/join/{code}` (session now holds `invitation_code`).
  3. `POST /register` with attacker email/password (override prefilled email).
  4. Controller attaches `invite->role_id` and logs attacker in with that role.
- **Evidence:**
  - `src/Http/Controllers/Auth/InvitationsController.php:173` — session put without later email enforcement on register
  - `src/Http/Controllers/Auth/RegisteredUserController.php:35-72` — role attach with no `$request->email === $invite->email`
  - `resources/views/auth/register_form_fields.blade.php:20-21` — editable email, only `old(..., $prefilledEmail)`
  - `src/Http/Controllers/Auth/LoginController.php:66-92` — login claim also unbound to invite email
- **Remediation:** Require `strtolower($request->email) === strtolower($invite->email)` before attaching roles or claiming. Prefer single-use signed URLs; clear session code on mismatch; force email field read-only/server-side override from invite.

---

### 3. Inverted `delete-users` check enables deleting privileged users

- **Severity:** HIGH
- **Primary location:** `src/Http/Controllers/Auth/UsersController.php`
- **Description:** `destroy()` calls `$user->cant('delete-users')` on the **target** user, not the authenticated actor. Regular users (without `delete-users`) cannot be deleted; users who *have* `delete-users` (Super-Admins / Developers) can be deleted. The Blade UI correctly checks the actor (`$user->can('delete-users')`), so the server and UI disagree.
- **Impact:** An Admin can permanently delete Super-Admin accounts while being unable to delete ordinary users. Combined with finding #1, Admins can take over and destroy higher-privilege accounts.
- **Attack path:**
  1. Authenticate as Admin (has `delete-users` via user-management category).
  2. `DELETE /account/users/{superAdminId}`.
  3. Server evaluates target Super-Admin `cant('delete-users')` → false → deletion proceeds (`stripPIIDAndDelete`).
- **Evidence:**
  - `src/Http/Controllers/Auth/UsersController.php:282-301` — `$user` is the target; `cant('delete-users')` inverted
  - `resources/views/manage/users/index.blade.php:75` — UI checks actor correctly
- **Remediation:** Check `auth()->user()->cant('delete-users')` (or policy). Add hierarchy rules so lower roles cannot delete higher roles. Add automated tests for both positive and negative cases.

---

### 4. Public registration POST ignores `REGISTRATIONS_ENABLED`

- **Severity:** HIGH
- **Primary location:** `publish/routes/web.php`
- **Description:** `REGISTRATIONS_ENABLED` / `features.auth.public_users_can_register` (default `false`) only gates the **GET** `/register` form. `POST /register` → `RegisteredUserController@store` is always registered “for invitations,” but the controller does **not** require a valid invitation when `session('invitation_code')` is absent. The legacy `RegisterController` had an explicit registrations-disabled check; the Fortify-based controller does not. Successful registration attaches `assign_by_default` roles (seeded “Users”) and logs the user in.
- **Impact:** When operators believe registration is disabled, attackers can still create accounts and obtain authenticated sessions (spam, abuse, foothold for further bugs).
- **Attack path:**
  1. Confirm GET `/register` is unavailable / feature flag false.
  2. `POST /register` with valid `name`, `email`, `password`, `password_confirmation` (CSRF token from any web page).
  3. Account created, default “Users” role attached, session established.
- **Evidence:**
  - `publish/routes/web.php:86-106` — GET gated; POST always open
  - `src/Http/Controllers/Auth/RegisteredUserController.php:23-100` — invitation optional; no feature-flag check
  - `src/Http/Controllers/Auth/RegisterController.php:99-101` — legacy controller *did* enforce disable
  - `publish/config/features.php:27` — default `REGISTRATIONS_ENABLED` false
- **Remediation:** In `store()`, require either `has_feature('auth.public_users_can_register')` **or** a valid unused invitation in session (with email bind). Reject all other attempts with 403.

---

### 5. Disabled users can still authenticate via API login

- **Severity:** HIGH
- **Primary location:** `src/Http/Controllers/API/V1/Auth/AuthController.php`
- **Description:** API `login` uses `auth()->attempt()` only. There is no post-auth check of `disabled_at` / `isDisabled()`, despite `OxygenUserTrait` defining disable semantics used by the admin UI.
- **Impact:** “Disable account” does not revoke API access; disabled users keep obtaining `access_token`s and calling authenticated API routes.
- **Attack path:**
  1. Admin disables user via manage users.
  2. Disabled user `POST /api/.../login` with email/password + device fields + `x-api-key`.
  3. Login succeeds; fresh access token returned.
- **Evidence:**
  - `src/Http/Controllers/API/V1/Auth/AuthController.php:207-233` — attempt then issue token; no disabled check
  - `src/Entities/Traits/OxygenUserTrait.php:148-150,186-198` — disable model exists
  - `src/Http/Controllers/Auth/UsersController.php:239-252` — UI disable path
- **Remediation:** After successful attempt, if `$user->isDisabled()` then fail closed, clear tokens, and return 403. Also gate web/Fortify login the same way.

---

### 6. Private files readable by any authenticated user (IDOR)

- **Severity:** HIGH
- **Primary location:** `publish/app/Http/Controllers/Manage/ManageFilesController.php`
- **Description:** For files with `allow_public_access = false`, `publicView` only requires *some* authenticated web user or device access token. There is no ownership, role, or ACL check (explicit TODO). UUID secrecy is not authorization; UUIDs appear in admin UI and permalinks.
- **Impact:** Any logged-in user (including default “Users” role) who learns a file UUID can download private admin uploads.
- **Attack path:**
  1. Obtain UUID (UI leak, referrer, logs, shared link without intending auth bypass).
  2. Authenticate as any normal user (or use registration bypass #4).
  3. `GET /files/{uuid}` → file streamed.
- **Evidence:**
  - `publish/app/Http/Controllers/Manage/ManageFilesController.php:29-45` — auth presence only; TODO for real checks
  - `publish/routes/web.php:83` — public file route
- **Remediation:** Enforce allowlist (uploader, roles with `manage-file-uploads`, or explicit ACL). Deny by default for private files.

---

### 7. Unrestricted admin file upload served inline → stored XSS

- **Severity:** MEDIUM
- **Primary location:** `src/Http/Controllers/Manage/ManageFilesController.php`
- **Description:** Upload validation is only `required|file` (no mime/extension allowlist). Files are served inline via `response()->file()` in `show` / `publicView`. With `allow_public_access`, unauthenticated browsers execute HTML/SVG in the app origin.
- **Impact:** Malicious/compromised Admin/Developer plants stored XSS affecting Super-Admins or end users (in-browser actions as the victim).
- **Attack path:**
  1. Authenticate as `admins` or `developers`.
  2. Upload `xss.html` / `.svg`; enable public access.
  3. Victim opens `/files/{uuid}/xss.html` (or manage view URL).
  4. Script runs in app origin.
- **Evidence:**
  - `src/Http/Controllers/Manage/ManageFilesController.php:91-95,234-244`
  - `publish/app/Http/Controllers/Manage/ManageFilesController.php:23-45`
  - `publish/routes/web.php:83`
- **Remediation:** Allowlist types; force `Content-Disposition: attachment` + safe Content-Type; block HTML/SVG; separate content domain; re-enable upload permission middleware.

---

### 8. Last-admin protection uses wrong role slugs (lockout)

- **Severity:** MEDIUM
- **Primary location:** `src/Http/Controllers/Auth/Groups/GroupsController.php`
- **Description:** `destroyUser` checks role names `super-admin` / `admin`, but seeded slugs are `super-admins` / `admins`. Guard never matches.
- **Impact:** User with `delete-groups` can remove the last Super-Admin from the role and lock out the dashboard.
- **Attack path:**
  1. As Developer/Super-Admin, remove last user from `super-admins` role via group users destroy.
  2. Name check fails open.
- **Evidence:**
  - `src/Http/Controllers/Auth/Groups/GroupsController.php:268-277`
  - `publish/database/seeders/Auth/RolesTableSeeder.php:30-37`
  - `publish/routes/oxygen-web.php:133`
- **Remediation:** Match real role `name` values; block removal of last member of any dashboard-capable role.

---

### 9. API password-reset responses enable email enumeration

- **Severity:** MEDIUM
- **Primary location:** `src/Http/Controllers/API/V1/Auth/ForgotPasswordController.php`
- **Description:** Success and failure responses differ (`apiSuccess` vs `apiError` with “ensure your email is correct”). Combined with only generic API throttling, attackers can probe which emails are registered.
- **Impact:** Account enumeration aids phishing and credential stuffing.
- **Attack path:**
  1. With valid `x-api-key`, `POST` password reset for candidate emails.
  2. Distinguish 200 success body vs error body.
- **Evidence:**
  - `src/Http/Controllers/API/V1/Auth/ForgotPasswordController.php:45-71`
- **Remediation:** Always return the same success message; add dedicated rate limits per IP/email; avoid confirming account existence.

---

## High-confidence candidates (not fully confirmed)

| Candidate | Why not fully confirmed |
|-----------|-------------------------|
| Account profile/password/email routes under Fortify `web` middleware **without** `auth` (`oxygen-web.php:56-62`) | Guests hit null `Auth::user()` and error; logged-in abuse still needs CSRF. Missing defense-in-depth. |
| `DISABLE_DASHBOARD_SECURITY` auto-login (`Authenticate.php:37-50`) | Only `local`/`testing`; installer wiring of Oxygen `Authenticate` is commented out. Dangerous if both are enabled on mislabeled prod. |
| API `POST /register` ignores `REGISTRATIONS_ENABLED` | May be intentional for mobile; inconsistent with web flag when `API_ACTIVE=true`. |
| Web/Fortify login also skips `isDisabled()` | Likely same bug as API #5; Fortify pipeline not fully traced in-host. |
| `ParseInputStream` logs raw multipart bodies / `sha1(rand())` temps | Needs log exposure; UploadedFile ctor looks broken on modern Laravel. |
| `GET /logout` CSRF logout (`web.php:79`) | Real but low impact. |
| Invitations: any `role_id` including Super-Admins (`InvitationsController::send` TODO) | Requires `invite-group-users`; may be intended for Super-Admins. |
| `updateRoleAbilities` client-supplied ability names | Actors already have `edit-permissions`; Bouncer auto-create not fully confirmed. |
| Docker/CI `MYSQL_PASSWORD` / `MYSQL_ROOT_PASSWORD` keys | Local/CI only; values omitted. |
| Admin docs page renders `API_KEY` into HTML | Admin-only; increases blast radius with XSS. |

## Areas checked with no validated exploitable chain

- **SQL:** Only `whereRaw`/`selectRaw` usage is `FiltersByLatLngTrait` with bound parameters; trait is **unused** elsewhere in this package.
- **Command injection:** Artisan scaffolding uses `$this->call` / filesystem APIs; no shell interpolation of HTTP input. `setup/pipeline.sh` `eval "$(ssh-agent)"` is local CI setup, not request-driven.
- **Deserialization / `eval` / `exec` / `system` / `shell_exec` / `unserialize`:** No dangerous request-driven usage in `src/`.
- **SSRF:** `setup/SyncFromSource.php` and `oxygen-install.php` fetch fixed/allowlisted installer URLs at setup time, not from HTTP clients.
- **Path traversal:** Manage file paths come from uploader/`PathResolver`, not raw user path strings in the reviewed controllers.
- **Blade `{!! !!}`:** Formation form renderers only; not direct echo of request input.
- **CORS / CSRF:** Web stack uses Laravel `VerifyCsrfToken`; no package-level `Access-Control-Allow-Origin: *` with credentials found. Session cookie flags rely on host Laravel defaults (not overridden insecurely here).
- **Hardcoded production secrets in app code:** None found as live prod credentials. CI/docker use named env defaults for tests (values omitted per policy).
