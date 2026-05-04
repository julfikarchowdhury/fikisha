<div class="row">
    <div class="col-md-12 ">
   
        <div class="form-group"> 
                <label for="title">{{ __('levels.title') }} <span class="text-danger">*</span> </label>
                <input id="title" type="text" name="data[title]" placeholder="{{ __('levels.Enter_title') }}"
                    class="form-control @error('title') is-invalid @enderror"
                    value="{{ old('title', @$section['title']) }}" required>
                @error('title')
                    <small class="text-danger mt-2">{{ $message }}</small>
                @enderror 
        </div>

        <div class="form-group">
            <label for="description">{{ __('levels.description') }}</label> <span class="text-danger">*</span>
            <textarea id="description" type="text" name="data[description]"
                placeholder="{{ __('levels.Enter_title') }}" autocomplete="off"
                class="form-control @error('description') is-invalid @enderror" required>{{ old('description', @$section['description']) }}</textarea>
            @error('description')
                <small class="text-danger mt-2">{{ $message }}</small>
            @enderror
        </div>
           
    </div>

    <div class="col-md-4 ">
        <div class="card">
            <div class="card-body">
                <h3 class="mb-0"  >{{ __('levels.step') }} 1</h3>
                <div class="form-group"> 
                    <div class="text-center">
                        <img src="{{ @$section['icon_1'] }}" width="70px" height="70px"/>
                    </div>
                    <label for="icon_1">{{ __('levels.icon') }} <span class="text-danger">*</span>  </label>
                    <input id="icon_1" type="file" name="data[icon_1]" placeholder="{{ __('levels.Enter_icon') }}"
                        autocomplete="off" class="form-control @error('icon_1') is-invalid @enderror"
                        value="{{ old('icon_1', @$section['icon_1']) }}" >
                    @error('icon_1')
                        <small class="text-danger mt-2">{{ $message }}</small>
                    @enderror 
                </div>

                <div class="form-group">
                    <label for="title_1">{{ __('levels.title') }}</label> <span class="text-danger">*</span>
                    <input id="title_1" type="text" name="data[title_1]"
                        placeholder="{{ __('levels.Enter_title') }}" autocomplete="off"
                        class="form-control @error('title_1') is-invalid @enderror"
                        value="{{ old('title_1', @$section['title_1']) }}" required>
                    @error('title_1')
                        <small class="text-danger mt-2">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">

        <div class="card">
            <div class="card-body">

                <h3 class="mb-0"   >{{ __('levels.step') }} 2</h3>
                <div class="row">
                    <div class="form-group ">
                        <div class="text-center">
                            <img src="{{ @$section['icon_2'] }}" width="70px" height="70px"/>
                        </div>
                        <label for="icon_2">{{ __('levels.icon') }} <span class="text-danger">*</span>  </label>
                        <input id="icon_2" type="file" name="data[icon_2]"
                            placeholder="{{ __('levels.Enter_icon') }}" autocomplete="off"
                            class="form-control @error('icon_2') is-invalid @enderror"
                            value="{{ old('icon_2', @$section['icon_2']) }}" >
                        @error('icon_2')
                            <small class="text-danger mt-2">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group ">
                        <label for="title_2">{{ __('levels.title') }}</label> <span class="text-danger">*</span>
                        <input id="title_2" type="text" name="data[title_2]"
                            placeholder="{{ __('levels.Enter_title') }}" autocomplete="off"
                            class="form-control @error('title_2') is-invalid @enderror"
                            value="{{ old('title_2', @$section['title_2']) }}" required>
                        @error('title_2')
                            <small class="text-danger mt-2">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h3 class="mb-0"   >{{ __('levels.step') }} 3</h3>
                <div class="row">
                    <div class="form-group ">
                        <div class="text-center">
                            <img src="{{ @$section['icon_3'] }}" width="70px" height="70px"/>
                        </div>
                        <label for="icon_3">{{ __('levels.icon') }} <span class="text-danger">*</span>  </label>
                        <input id="icon_3" type="file" name="data[icon_3]"
                            placeholder="{{ __('levels.Enter_icon') }}" autocomplete="off"
                            class="form-control @error('icon_3') is-invalid @enderror"
                            value="{{ old('icon_3', @$section['icon_3']) }}" >
                        @error('icon_3')
                            <small class="text-danger mt-2">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group ">
                        <label for="title_3">{{ __('levels.title') }}</label> <span class="text-danger">*</span>
                        <input id="title_3" type="text" name="data[title_3]"
                            placeholder="{{ __('levels.Enter_title') }}" autocomplete="off"
                            class="form-control @error('title_3') is-invalid @enderror"
                            value="{{ old('title_3', @$section['title_3']) }}" required>
                        @error('title_3')
                            <small class="text-danger mt-2">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h3 class="mb-0"   >{{ __('levels.step') }} 4</h3>
                <div class="row">
                    <div class="form-group">
                        <div class="text-center">
                            <img src="{{ @$section['icon_4'] }}" width="70px" height="70px"/>
                        </div>
                        <label for="icon_4">{{ __('levels.icon') }} <span class="text-danger">*</span>  </label>
                        <input id="icon_4" type="file" name="data[icon_4]"
                            placeholder="{{ __('levels.Enter_icon') }}" autocomplete="off"
                            class="form-control @error('icon_4') is-invalid @enderror"
                            value="{{ old('icon_4', @$section['icon_4']) }}" >
                        @error('icon_4')
                            <small class="text-danger mt-2">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="title_4">{{ __('levels.title') }}</label> <span class="text-danger">*</span>
                        <input id="title_4" type="text" name="data[title_4]"
                            placeholder="{{ __('levels.Enter_title') }}" autocomplete="off"
                            class="form-control @error('title_4') is-invalid @enderror"
                            value="{{ old('title_4', @$section['title_4']) }}" required>
                        @error('title_4')
                            <small class="text-danger mt-2">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">

        <div class="card">
            <div class="card-body">

                <h3 class="mb-0"  >{{ __('levels.step') }} 5</h3>
                <div class="row">
                    <div class="form-group ">
                        <div class="text-center">
                            <img src="{{ @$section['icon_5'] }}" width="70px" height="70px"/>
                        </div>
                        <label for="icon_5">{{ __('levels.icon') }} <span class="text-danger">*</span>  </label>
                        <input id="icon_5" type="file" name="data[icon_5]"
                            placeholder="{{ __('levels.Enter_icon') }}" autocomplete="off"
                            class="form-control @error('icon_5') is-invalid @enderror"
                            value="{{ old('icon_5', @$section['icon_5']) }}" >
                        @error('icon_5')
                            <small class="text-danger mt-2">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group ">
                        <label for="title_5">{{ __('levels.title') }}</label> <span class="text-danger">*</span>
                        <input id="title_5" type="text" name="data[title_5]"
                            placeholder="{{ __('levels.Enter_title') }}" autocomplete="off"
                            class="form-control @error('title_5') is-invalid @enderror"
                            value="{{ old('title_5', @$section['title_5']) }}" required>
                        @error('title_5')
                            <small class="text-danger mt-2">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
