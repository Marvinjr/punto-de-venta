new Vue({
    el: '#product-tax-report',
    data: {
        startDate: tendoo.date.format( 'YYYY-MM-DD' ),
        endDate: tendoo.date.format( 'YYYY-MM-DD' ),
        ...taxReportData,
        rawEntries: []
    },
    watch: {
        ...hookedWatch
    },
    computed: {
        ...hookedComputed,

        entries() {
            const endDate       =   moment( this.endDate, 'YYYY-MM-DD' );
            const startDate     =   moment( this.startDate, 'YYYY-MM-DD' );
            const days          =   Math.abs( startDate.diff( endDate, 'days' ) );
            const results       =   [];
            
            for( let i = 0; i <= days; i++ ) {

                const today             =   startDate.clone().add( i, 'days' );
                const taxes             =   this.systemTaxes.map( tax => Object.assign({}, tax ) );

                /**
                 * filter items by the current 
                 * date on the loop
                 */
                const items             =   this.rawEntries.filter( order => {
                    return (
                        moment( order.DATE_MOD ).isSameOrAfter( today.startOf( 'day' ) ) &&
                        moment( order.DATE_MOD ).isSameOrBefore( today.endOf( 'day' ) )
                    );
                }).map( order => order.items ).flat();

                /**
                 * here we are retreiving the tax of a specific items and save
                 * it as the daily (loop) tax. This includes TIMES (how many time the tax is found)
                 * and the total value. We make sure a taxes are distinct and compute total of similar taxes.
                 */
                items.forEach( item => {
                    const savedTaxesId  =   taxes.map( tax => tax.ID );
                    item.metas          =   item.metas === undefined ? {} : item.metas;
                    item.metas.tax      =   typeof item.metas.tax === 'string' ? JSON.parse( item.metas.tax ) : item.metas.tax;
                    
                    if ( item.metas.tax ) {
                        if ( savedTaxesId.includes( item.metas.tax.ID ) ) {
                            taxes.forEach( tax => {
                                if ( parseInt( tax.ID ) === parseInt( item.metas.tax.ID ) ) {
                                    tax.TOTAL_VALUE   +=  parseFloat( item.metas.tax.VALUE );
                                    tax.TIMES         +=  1;
                                }
                            });
                        } else {
                            taxes.push( Object.assign( item.metas.tax, {
                                TOTAL_VALUE     :   parseFloat( item.metas.tax.VALUE ),
                                TIMES           :   1
                            }))
                        }
                    }
                });

                results.push({
                    starts: today.startOf( 'day' ).format( 'YYYY-MM-DD HH:mm:ss' ),
                    ends: today.endOf( 'day' ).format( 'YYYY-MM-DD HH:mm:ss' ),
                    taxes
                });
            }

            return results;
        },

        collectedTaxes() {
            const savedTaxes    =   [];

            if ( this.entries.length > 0 ) {
                this.entries.forEach( entry => {
                    entry.taxes.forEach( tax => {
                        if ( ! savedTaxes.map( _savedTax => parseInt( _savedTax.id ) ).includes( parseInt( tax.ID ) ) ) {
                            savedTaxes.push({
                                id: tax.ID,
                                name: tax.NAME,
                                total:  tax.TOTAL_VALUE
                            });
                        } else {
                            savedTaxes.forEach( savedTax => {
                                if ( parseInt( savedTax.id ) === parseInt( tax.ID ) ) {
                                    savedTax.total  +=  tax.TOTAL_VALUE
                                }
                            });
                        }
        
                    })
                })
            }

            return savedTaxes;
        },

        overAllTaxes() {
            if ( this.collectedTaxes.length > 0 ) {
                return this.collectedTaxes.map( tax => tax.total )
                    .reduce( ( before, after ) => before + after );
            }
            return 0;
        },

        systemTaxes() {
            return this.allTaxes.map( tax => {
                tax.TOTAL_VALUE     =   0;
                tax.TIMES           =   0;
                return tax;
            })
        }
    },
    methods: {
        ...hookedMethods,

        /**
         * Define the start date of the report
         * @param {String} event 
         */
        changeStartDate( event ) {
            this.rawEntries     =   [];
            this.startDate      =   event;
        },

        /**
         * Define the End date of the report
         * @param {String} event 
         */
        changeEndDate( event ) {
            this.rawEntries     =   [];
            this.endDate        =   event;
        },

        /**
         * Compute the taxes for 
         * a specifc set of taxes
         * @param {Array<{TOTAL_VALUE: number}>} taxes
         * @return {number} computed taxes;
         */
        totalTaxes( taxes ) {
            if ( taxes.length > 0 ) {
                return taxes.map( tax => tax.TOTAL_VALUE )
                    .reduce( ( before, after ) => before + after );
            }
            return 0;
        },

        /**
         * load a report using 
         * the specified start and end date
         * @return void
         */
        getReport() {
            const isRightTimeRange  =   moment( this.startDate, 'YYYY-MM-DD' ).isSameOrBefore( moment( this.endDate, 'YYYY-MM-DD' ) );
            const isValid           =   ( moment( this.startDate, 'YYYY-MM-DD' ).isValid() && moment( this.endDate, 'YYYY-MM-DD' ).isValid() )

            if ( ! isValid ) {
                return swal({
                    title: this.textDomain.cantProceed,
                    text: this.textDomain.wrongDates,
                    type: 'error'
                })
            }

            if ( ! isRightTimeRange ) {
                return swal({
                    title: this.textDomain.cantProceed,
                    text: this.textDomain.wrongTimeRange,
                    type: 'error'
                })
            }

            HttpRequest.post( this.url.get, {
                startDate: this.startDate,
                endDate: this.endDate
            }).then( result => {
                this.rawEntries     =   result.data;
            }).catch( result => {
                swal({
                    title: this.textDomain.errorOccured,
                    text: result.response.data.message || this.textDomain.unexpectedError
                })
            })
        }
    }
})