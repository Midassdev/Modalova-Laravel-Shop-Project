<footer class="footer">

  <div class="container">
    <div class="row">
      <div class="col-xs-12 no-padding">

        <nav class="footer-nav">

          <ul class="footer-nav-socials">
            @foreach([
              'ig' => config('app.link_to_instagram'),
              'facebook' => config('app.link_to_facebook'),
              'pin' => config('app.link_to_pinterest'),
              ] as $network => $link)
              <li class="{{ $network }}"><a rel="noopener" href="{{ $link }}" target="_blank"><i class="icon-{{ $network }}" arial-role="link" aria-label="{{ $network }}"></i></a></li>
            @endforeach
          </ul>

          <a href="{{ route('home') }}" class="footer-nav-logo">
            <img width="100" height="31" src="{{ asset('images/logo.svg', is_connection_secure()) }}" alt="Logo - {{ config('app.site_name') }}">
          </a>

          @yield('post__footer-nav')

          <p class="strong">{{ _i('Changer de pays :') }}</p>
          <ul class="footer-nav-languages">
            @foreach(config('locales') as $locale => $data)
              @php if($locale == config('app.locale')) continue @endphp
              <li><a href="{{ $data['url'] }}" target="_blank" title="{{ _i("%s en %s", [
                  config('app.name'),
                  "{$data['flag']} {$data['name']}"
                ]) }}">{{ "{$data['flag']} {$data['name']}" }}</a></li>
              @endforeach
          </ul>

        </nav>

      </div>
    </div>

  </div>

  <div class="footer-content">

    <div class="footer-statics">

      <div class="footer-statics-links">
        <a href="{{ route('get.static.about') }}">{{ _i("À propos") }}</a>
        <a href="{{ route('get.static.faq') }}">{{ _i("F.A.Q") }}</a>
        <a href="mailto:{{ config('app.email') }}" target="_blank">{{ _i("Contactez-nous") }}</a>
        {{-- <a href="{{ route('get.static.cgu') }}">{{ _i("C.G.U") }}</a> --}}
        <a href="{{ route('get.static.legals') }}">{{ _i("Mentions légales") }}</a>
      </div>

      <div class="footer-statics-copyright container">{{ _i("© %d %s. All Rights Reserved.", [date('Y'), config('app.site_name')]) }}</div>

    </div>

  </div>


</footer>
