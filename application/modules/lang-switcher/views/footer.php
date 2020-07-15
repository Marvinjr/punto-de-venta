<script>
const LanguageSwitcherData  =   {
    textDomain: {
        language: `<?php echo __( 'Language', 'lang-switcher' );?>`,
        chooseLanguage: `<?php echo __( 'Choose Language', 'lang-switcher' );?>`,
    },
    url : {
        setLanguage: `<?php echo site_url([ 'api', 'lang-switcher', 'set' ]);?>`
    },
    rawLanguages: <?php echo json_encode( $this->config->item( 'supported_languages' ) );?>
}

$( document ).ready( function() {
    $( '.navbar-custom-menu>.nav.navbar-nav' ).prepend( 
        `<li class="dropdown notifications-menu" id="language-switcher">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                <i class="fa fa-globe"></i>
                ${LanguageSwitcherData.textDomain.language}
            </a>
            <ul class="dropdown-menu">
                <li class="header">${LanguageSwitcherData.textDomain.chooseLanguage}</li>
                <li>
                    <ul class="menu">
                        <li v-for="language of languages" @click="changeLanguage( language )">
                            <a href="javascript:void(0)">
                                {{ language.label }}
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>`
    );

    const LanguageSwitcher  =   new Vue({
        el: '#language-switcher',
        mounted() {
        },
        methods: {
            changeLanguage( language ) {
                HttpRequest.post( this.url.setLanguage, { lang : language.value }).then( result => {
                    NexoAPI.Toast()( result.data.message );
                    setTimeout( () => {
                        location.reload();
                    }, 500 );
                }).catch( error => {
                    console.log( error );
                })
            }
        },
        computed: {
            languages() {
                const finalLanguage     =   [];
                for( let lang in this.rawLanguages ) {
                    finalLanguage.push({
                        label: this.rawLanguages[ lang ],
                        value: lang
                    });
                }

                return finalLanguage;
            }
        },
        data: {
            ...LanguageSwitcherData
        }
    })
});
</script>