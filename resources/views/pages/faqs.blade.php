@extends('oxygen::layouts.master-frontend-internal')

@push('meta')
    <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW" />
@endpush

@section('internal-page-contents')
    <div>
        <h3>Tech Innovation In Australia</h3>
        <p>The Australian startup economy is entering an exciting new era of innovation and tremendous growth. The government has encouraged more risk takers to make their ideas a successful and sustainable business, there is never a better moment in the country. As a consequence, more and more startups are surfacing with innovative ideas for existing and new business models.
        </p>

        <h3>Why Invest In Tech Startups?</h3>
        <p>Australia has always produced a steady stream of entrepreneurs and innovators with a global profile. Some startups have redefined the limits of what’s possible:
        </p>

        <ul>
            <li>Atlassian, started in 2002 with $AUD13,000 credit card and now it’s a $AUD8 Billion company.</li>
            <li>Google Maps was founded by 2 brothers at a Sydney based company and acquired by the tech giant Google.</li>
            <li>Locomote a Melbourne-based corporate travel tech company that raised the investment to 55%.</li>
            <li>Spacer is a peer-to-peer and B2C marketplace for storage space founded in October 2015 with an initial seed capital of $AUD1.6 million. The company is expanding into Asia’s $AUD5 billion market.</li>
        </ul>

        <p>According to recent reports, Australia tech growth has outpaced US and UK. The health of app development is a good indicator of how well other sectors are embracing the digital world. Moreover, creative thinkers in Australia have a great opportunity to explore a variety of solutions for any market niche.
        </p>

        <h3>How Much Can You Invest In Tech Startups?</h3>
        <p>As little or as much as you want. We introduce you to promising startup ideas, founders and teams. We don't take any commission or charge fees for this. After the introduction, you can discuss how much you're comfortable with investing.
        </p>

        <h3>What Do You Do at {{ config('app.name') }}?</h3>
        <ul>
            <li>We help to build new and innovative mobile apps and tech businesses</li>
            <li>We match promising new startup ideas with investors</li>
            <li>We market and promote these projects</li>
        </ul>

        <p>* Currency converted from USD to AUD dollar</p>

        <div class="row text-center">
            <br>
            <h3>Register or Signup Now to Get Started</h3>
            <br><br>

            @include('partials.signupOptions')
        </div>

    </div>
@stop