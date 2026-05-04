<!-- banner -->
<section class="container-fluid pb-3  ">
    <div class="container pt-5 pb-5 ">
        <div class="row align-items-center mt-3">
            <div class="col-lg-6">
             
                <form action="{{ route('tracking.index') }}" method="get">
                    <div class="input-group mb-3 tracking-form">
                        <input type="text" class="form-control" placeholder="{{ __('levels.enter_tracking_id') }}" name="tracking_id"  >
                        <div class="input-group-append">
                        <button type="submit" class="input-group-text bg-primary"  >{{ __('levels.track_now') }}</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-lg-6">
                <div class="position-relative offset-lg-1 " data-cue="slideInDown" data-show="true" >
                    @if(section(\App\Enums\SectionType::BANNER,'banner'))
                        <img src="{{ section(\App\Enums\SectionType::BANNER,'banner') }}" class="banner-image" />
                    @endif
                </div>
            </div> 
        </div>
    </div>
</section>