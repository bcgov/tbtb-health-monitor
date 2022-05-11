<template>
    <div class="row">
        <div class="col-md-3 mb-3">
        </div>
        <div class="col-md-9">
            <ul v-if="logs != ''" class="list-group">
                <template v-for="l in logs">
                    <li v-if="l.message != ''" class="list-group-item"><strong>({{ l.datetime }})</strong> {{l.message}}</li>
                </template>
            </ul>
        </div>

    </div>
</template>
<style scoped>
svg{
    cursor: pointer;
}
svg path{
    fill: #fff;
}
</style>
<script>
import axios from 'axios';

export default {
    filters: {

        formatAppNumber: function(value){
            let year = value.slice(0, 4);
            let extra = value.slice(4);

            return year + '-' + extra;
        }
    },

    data: () => ({
        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        logs: '',
    }),
    props: [],
    methods: {


        fetchData: function(){

        },

    },
    mounted() {
        let vm = this;
        axios({
            url: '/fetch-logs',
            method: 'get',
            headers: {'Accept': 'application/json'}
        })

            .then(function (response) {
                vm.logs = response.data.logs;
            })
            .catch(function (error) {
                console.log(error);
            });
    }
}

</script>
