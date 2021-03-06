Vue.component( 'num-keyboard', {
    data: () => {
        return {};
    },
    props:  [ 'screen', 'config' ],
    methods: {
        emitClick( key ) {
            this.$emit( 'press', key );
        }
    },
    mounted() {
        console.log( this.screen );
    },
    watch: {
        screen() {
            this.$emit( 'value', this.screen );
        }
    },
    computed: {
        modalConfig() {
            return Object.assign({}, {
                hideInput: false
            }, this.config || {})
        }
    },
    template: `
    <div class="keyboard-wrapper w-100 d-flex flex-column" style="flex: 1 0 auto;">    
        <slot name="input">
            <div  class="input-group input-group-lg p-2 rounded-0">
                <input style="line-height: 50px;font-size: 40px;height: 80px;" v-model="screen" type="text" class="input-field form-control rounded-0 input-lg" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg">
            </div>
        </slot>
        <hr class="m-0 border-1">
        <div class="btn-group-vertical m-0 flex-fill w-100 d-flex flex-column" role="group" style="flex: 1 0 auto;">
            <div class="btn-group flex-fill" style="flex: 1 auto">
                <slot name="first">
                    <button @click="emitClick(7)" type="button" class="btn btn-outline-primary border-bottom-0 border-top-0 rounded-0 border-left-0">
                        <h1 class="m-0">7</h1>
                    </button>
                    <button @click="emitClick(8)" type="button" class="btn btn-outline-primary border-bottom-0 border-top-0">
                        <h1 class="m-0">8</h1>
                    </button>
                    <button @click="emitClick(9)" type="button" class="btn btn-outline-primary border-bottom-0 border-top-0 rounded-0">
                        <h1 class="m-0">9</h1>
                    </button>
                    <button @click="emitClick('x')" type="button" class="btn btn-outline-primary border-bottom-0 border-top-0 rounded-0 border-right-0">
                        <h1 class="m-0">&times;</h1>
                    </button>
                </slot>
            </div>
            <div class="btn-group flex-fill" style="flex: 1 auto">
                <slot name="second">
                    <button @click="emitClick(4)" type="button" class="btn btn-outline-primary border-bottom-0 rounded-0 border-left-0">
                        <h1 class="m-0">4</h1>
                    </button>
                    <button @click="emitClick(5)" type="button" class="btn btn-outline-primary border-bottom-0">
                        <h1 class="m-0">5</h1>
                    </button>
                    <button @click="emitClick(6)" type="button" class="btn btn-outline-primary border-bottom-0 rounded-0">
                        <h1 class="m-0">6</h1>
                    </button>
                    <button @click="emitClick('+')" type="button" class="btn btn-outline-primary border-bottom-0 rounded-0 border-right-0">
                        <h1 class="m-0">+</h1>
                    </button>
                </slot>
            </div>
            <div class="btn-group flex-fill" style="flex: 1 auto">
                <slot name="third">
                    <button @click="emitClick(1)" type="button" class="btn btn-outline-primary border-bottom-0 rounded-0 border-left-0">
                        <h1 class="m-0">1</h1>
                    </button>
                    <button @click="emitClick(2)" type="button" class="btn btn-outline-primary border-bottom-0">
                        <h1 class="m-0">2</h1>
                    </button>
                    <button @click="emitClick(3)" type="button" class="btn btn-outline-primary border-bottom-0 rounded-0">
                        <h1 class="m-0">3</h1>
                    </button>
                    <button @click="emitClick('-')" type="button" class="btn btn-outline-primary border-bottom-0 rounded-0 border-right-0">
                        <h1 class="m-0">-</h1>
                    </button>
                </slot>
            </div>
            <div class="btn-group flex-fill" style="flex: 1 auto">
                <slot name="fourth">
                    <button @click="emitClick('±')" type="button" class="btn btn-outline-primary border-bottom-0 rounded-0 border-left-0">
                        <h1 class="m-0">±</h1>
                    </button>
                    <button @click="emitClick(0)" type="button" class="btn btn-outline-primary border-bottom-0">
                        <h1 class="m-0">0</h1>
                    </button>
                    <button @click="emitClick('.')" type="button" class="btn btn-outline-primary border-bottom-0 rounded-0">
                        <h1 class="m-0">.</h1>
                    </button>
                    <button @click="emitClick('=')" type="button" class="btn btn-outline-primary border-bottom-0 rounded-0 border-right-0">
                        <h1 class="m-0">=</h1>
                    </button>
                </slot>
            </div>
        </div>
    </div>
    `
})