@php
$data['categories'] = @$data['categories'] ?: get_categories_as_tree();
@endphp

<nav class="big-menu__section big-menu__main-menu big-menu__menu">
  <ul class="big-menu__main-menu__items">
    @foreach([
      App\Models\Gender::GENDER_FEMALE,
      App\Models\Gender::GENDER_MALE,
    ] as $gender)
      <li class="big-menu__main-menu__item">
        <a href="{{ route('get.products.byGender', ['gender' => _i($gender)]) }}" class="big-menu__main-menu__link">{{ _i($gender) }}</a>

        @if($gender == @$data['gender'])
          <ul class="big-menu__sub-menu big-menu__menu" style="display: block;">
            <li class="big-menu__sub-menu__item">
              <a href="{{ route('get.products.byPromotion.byGender', ['gender' => _i($gender)]) }}"
                class="big-menu__sub-menu__link big-menu__sub-menu__link--red">{{ get_current_sales_period() ?: _i('Promotions 🔥') }}</a>
            </li>

            @foreach($data['categories'] as $category) @if(App\Models\Gender::areMatching($gender, $category->gender))
              <li class="big-menu__sub-menu__item">
                <a href="{{ route('get.products.byGender.byCategory', ['gender' => _i($gender), 'category' => $category->slug]) }}" class="big-menu__sub-menu__link">{{ $category->title }}</a>
              </li>
            @endif @endforeach
          </ul>
        @endif
      </li>
    @endforeach

    <li class="big-menu__main-menu__item">
      <a href="{{ route('get.brands.index') }}" class="big-menu__main-menu__link">{{ _i("Marques") }}</a>
    </li>

    @if(config('features.enable_blog'))
      <li class="big-menu__main-menu__item">
        <a href="{{ url('/zine/') }}" class="big-menu__main-menu__link" target="_blank">{{ _i("Magazine") }}</a>
      </li>
    @endif

  </ul>
</nav>
