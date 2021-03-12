@props([
    'data' => [],
    'placeholder' => 'Select an option',
    'limit' => 40,
])

<div
    x-data="AlpineSelect({
        data: {{ json_encode($data) }},
        selected:  @entangle($attributes->wire('model')),
        placeholder: '{{ $placeholder }}',
        multiple: {{ isset($attributes['multiple']) ? 'true':'false' }},
        disabled: {{ isset($attributes['disabled']) ? 'true':'false' }},
        limit: {{ $limit }},
    })"
    x-init="init()"
    @click.away="closeSelect()"
    @keydown.escape="closeSelect()"
    @keydown.arrow-down.prevent="increaseIndex()"
    @keydown.arrow-up.prevent="decreaseIndex()"
    @keydown.enter="selectOption(Object.keys(options)[currentIndex])"
>

    <button
        class="rounded-md border content-center p-1 bg-white relative sm:text-sm sm:leading-5 w-full text-left"
        x-bind:class="{'border-blue-300 ring ring-blue-200 ring-opacity-50':open, 'bg-gray-200 cursor-default':disabled}"
        @click.prevent="toggleSelect()"
    >
        <div id="placeholder">
            <div class="m-1 inline-block" x-show="selected.length === 0" x-text="placeholder">&nbsp;</div>
        </div>
        @isset($attributes['multiple'])
            <div class="flex flex-wrap space-x-1" x-cloak x-show="selected.length > 0">
                <template x-for="(key, index) in selected" :key="index">
                    <div class="text-gray-800 rounded-full truncate bg-blue-300 px-2 py-0.5 my-0.5 flex flex-row items-center">
                        <div class="px-2 truncate" x-text="data[key]"></div>
                        <div x-show="!disabled" x-bind:class="{'cursor-pointer':!disabled}" class="w-4" @click.prevent.stop="deselectOption(index)"><x-icon.x class="h-4"/></div>
                    </div>
                </template>
            </div>
        @else
            <div class="flex flex-wrap" x-cloak x-show="selected">
                <div class="text-gray-800 rounded-full truncate bg-blue-300 px-2 py-0.5 my-0.5 flex flex-row items-center">
                    <div class="px-2 truncate" x-text="data[selected]"></div>
                    <div x-show="!disabled" x-bind:class="{'cursor-pointer':!disabled}" class="h-4" @click.prevent.stop="deselectOption()"><x-icon.x class="h-4"/></div>
                </div>
            </div>
        @endif

        <div
            class="mt-0.5 w-full bg-white border-gray-300 rounded-b-md border absolute top-full left-0 z-30"
            x-show="open"
            x-cloak
        >

            <div class="bg-white p-2 w-full relative z-30">
                <x-input.base type="search" x-model="search" x-on:click.prevent.stop="open=true"/>
            </div>

            <div x-ref="dropdown" class="p-2 max-h-60 overflow-y-auto relative z-30" >
                <div x-cloak x-show="Object.keys(options).length === 0" x-text="emptyOptionsMessage">Gragr</div>
                <template x-for="(key, index) in Object.keys(options)" :key="index" >
                    @isset($attributes['multiple'])
                    <div
                        class="px-2 py-1"
                        x-bind:class="{'bg-gray-300 text-white hover:none':selected.includes(key), 'hover:bg-blue-500 hover:text-white cursor-pointer':!(selected.includes(key)), 'bg-blue-500 text-white':currentIndex==index}"
                        @click.prevent.stop="selectOption(key)"
                        x-text="Object.values(options)[index]">
                    </div>
                    @else
                    <div
                        class="px-2 py-1"
                        x-bind:class="{'bg-gray-300 text-white hover:none':selected==key, 'hover:bg-blue-500 hover:text-white cursor-pointer':!(selected==key), 'bg-blue-500 text-white':currentIndex==index}"
                        @click.prevent.stop="selectOption(key)"
                        x-text="Object.values(options)[index]">
                    </div>
                    @endisset
                </template>
            </div>
        </div>
    </button>
</div>

@once
    <script>
        function AlpineSelect(config) {
            return {
                data: config.data ?? [],
                open: false,
                search: '',
                options: {},
                emptyOptionsMessage: 'No results match your search.',
                placeholder: config.placeholder,
                selected: config.selected,
                multiple: config.multiple,
                currentIndex: 0,
                isLoading: false,
                disabled: config.disabled ?? false,
                limit: config.limit ?? 40,

                init: function() {
                    if(this.selected == null ){
                        if(this.multiple)
                            this.selected = []
                        else
                            this.selected = ''
                    }
                    if(!this.data) this.data = {}


                    this.resetOptions()

                    this.$watch('search', ((values) => {
                        if (!this.open || !values) {
                            this.resetOptions()
                            return
                        }

                        this.options = Object.keys(this.data)
                            .filter((key) => this.data[key].toLowerCase().includes(values.toLowerCase()))
                            .slice(0, this.limit)
                            .reduce((options, key) => {
                                options[key] = this.data[key]
                                return options
                            }, {})


                        this.currentIndex=0
                    }))

                },

                resetOptions: function() {
                    this.options = Object.keys(this.data)
                        .slice(0,this.limit)
                        .reduce((options, key) => {
                            options[key] = this.data[key]
                            return options
                        }, {})
                },

                closeSelect: function() {
                    this.open = false
                    this.search = ''
                },

                toggleSelect: function() {
                    if(!this.disabled) {
                        if (this.open) return this.closeSelect()

                    this.open = true
                    }
                },

                deselectOption: function(index) {
                    if(this.multiple) {
                        this.selected.splice(index, 1)
                    }
                    else {
                        this.selected = ''
                    }

                },

                selectOption: function(value) {
                    if(!this.disabled) {
                        // If multiple push to the array, if not, keep that value and close menu
                        if(this.multiple){
                            // If it's not already in there
                            if (!this.selected.includes(value)) {
                                this.selected.push(value)
                            }
                        }
                        else {
                            this.selected=value
                            this.closeSelect()
                        }
                    }
                },

                increaseIndex: function() {
                    if(this.currentIndex == Object.keys(this.options).length)
                        this.currentIndex = 0
                    else
                        this.currentIndex++
                },

                decreaseIndex: function() {
                    if(this.currentIndex == 0)
                        this.currentIndex = Object.keys(this.options).length-1
                    else
                        this.currentIndex--;
                },
            }
        }
    </script>
    
@endonce
