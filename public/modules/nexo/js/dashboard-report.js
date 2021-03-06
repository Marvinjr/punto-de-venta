tendooApp.controller( 'dashboardReports', [ '$scope', '$http', function( $scope, $http ) {

    $scope.details      =   [];
    $scope.momentStarts =   moment( report.weekStarts );
    $scope.momentEnds   =   moment( report.weekEnds );
    
    /**
     * display the formated value
     * of the current date
     * @return string
    **/
    $scope.weekStarts   =   function() {
        return $scope.momentStarts.format( report.dateFormat || 'YYYY-MM-DD' );
    };

    /**
     * display the formated value
     * of the current date
     * @return string
    **/
    $scope.weekEnds     =   function() {
        return $scope.momentEnds.format( report.dateFormat || 'YYYY-MM-DD' );
    };

    /**
     * get reports
     * @param boolean require the report to be refreshed
     * @return void
     */
    $scope.getReport    =   function( refresh = false ) {
        $scope.refreshReport( refresh ).then( result => {
            $scope.details  =   result.data;
            // $scope.details[ result.data.day_of_week - 1 ]    =   result.data;
            $scope.loadLastSales();
        });
    }

    $scope.prevWeek     =   function() {
        $scope.momentStarts.subtract( 1, 'week' );
        $scope.momentEnds.subtract( 1, 'week' );
        $scope.getReport();
    }

    $scope.nextWeek     =   function() {
        const serverWeekInYear      =   moment( report.serverDate ).week();
        const weekInYear            =   $scope.momentStarts.week();

        if ( weekInYear === serverWeekInYear ) {
            return swal({
                title: report.textDomain.cantProceed,
                text: report.textDomain.weekReachLimit,
                type: 'error'
            });
        }

        $scope.momentStarts.add( 1, 'week' );
        $scope.momentEnds.add( 1, 'week' );
        $scope.getReport();
    }

    /**
     * Refresh report
     * @return {Promise} 
     */
    $scope.refreshReport    =   function( refresh ) {
        const range     =   `start=${$scope.momentStarts.format( 'YYYY-MM-DD' )}&end=${$scope.momentEnds.format( 'YYYY-MM-DD' )}`;
        return $http.get( `${report.week}&${range}${ refresh ? '&refresh=true' : '' }` );
    }

    /**
     * Load Last Sales
     */
    $scope.loadLastSales    =   function( result ) {
        $( '#dashboard-sales' ).replaceWith( '<canvas id="dashboard-sales" width="400" height="200"></canvas>' );
        var ctx = document.getElementById('dashboard-sales').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: report.labels,
                datasets: [{
                    label: report.totalPaid,
                    data: $scope.details.map( entry => entry.total_paid ),
                    backgroundColor: 'rgb(0, 166, 90, 1)',
                    borderColor: 'rgb(0, 166, 90, 1)',
                    borderWidth: 1
                },{
                    label: report.totalUnpaid,
                    data: $scope.details.map( entry => entry.total_unpaid ),
                    backgroundColor: 'rgb(166, 0, 0, 0.8)',
                    borderColor: 'rgb(166, 0, 0, 1)',
                    borderWidth: 1
                },{
                    label: report.totalPartially,
                    data: $scope.details.map( entry => entry.total_partially ),
                    backgroundColor: 'rgb(0, 82, 166, 0.8)',
                    borderColor: 'rgb(0, 82, 166, 1)',
                    borderWidth: 1
                }, {
                    label: report.totalRefunds,
                    data: $scope.details.map( entry => entry.total_refunds ),
                    backgroundColor: 'rgb(255, 116, 49)',
                    borderColor: 'rgb(220, 95, 35)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
        });
    }

    /**
     * get total paid
     * @return number
     */
    $scope.getTotalFor     =   function( type ) {
        if ( type == 'paid' ) {
            let totalPaid       =   0;
            $scope.details.map( entry => entry.total_paid ).forEach( amount => totalPaid += amount );
            return totalPaid;
        } else if (type == 'unpaid' ) {
            let totalUnpaid       =   0;
            $scope.details.map( entry => entry.total_unpaid ).forEach( amount => totalUnpaid += amount );
            return totalUnpaid;
        } else if (type == 'partially' ) {
            let totalPartially       =   0;
            $scope.details.map( entry => entry.total_partially ).forEach( amount => totalPartially += amount );
            return totalPartially;
        } else if (type == 'discount' ) {
            let totalDiscount       =   0;
            $scope.details.map( entry => entry.total_discount ).forEach( amount => totalDiscount += amount );
            return totalDiscount;
        } else if (type == 'taxes' ) {
            let totalTaxes       =   0;
            $scope.details.map( entry => entry.total_taxes ).forEach( amount => totalTaxes += amount );
            return totalTaxes;
        } else if (type == 'paid_nbr' ) {
            let paidNbr       =   0;
            $scope.details.map( entry => entry.paid_nbr ).forEach( amount => paidNbr += amount );
            return paidNbr;
        } else if (type == 'unpaid_nbr' ) {
            let unpaidNbr       =   0;
            $scope.details.map( entry => entry.unpaid_nbr ).forEach( amount => unpaidNbr += amount );
            return unpaidNbr;
        } else if (type == 'partially_nbr' ) {
            let partiallyNbr       =   0;
            $scope.details.map( entry => entry.partially_nbr ).forEach( amount => partiallyNbr += amount );
            return partiallyNbr;
        } else if ( type == 'total_refunds' ) {
            return $scope.details.length === 0 ? 0 :
            $scope.details
                .map( entry => {
                    /**
                     * this help to ensure compatibility 
                     * with old releases
                     */
                    if ( entry.total_refunds !== undefined ) {
                        return parseFloat( entry.total_refunds );
                    }
                    return 0;
                })
                .reduce( ( before, after ) => before + after )
        } else if ( type === 'refunds_count' ) {
            return $scope.details.length === 0 ? 0 : $scope.details
                .map( entry => {
                    /**
                     * this help to ensure compatibility 
                     * with old releases
                     */
                    if ( entry.refunds_count !== undefined ) {
                        return parseFloat( entry.refunds_count );
                    }
                    return 0;
                })
                .reduce( ( before, after ) => before + after );
        }
    }

    $scope.getReport();
}])