<template>
    <div class="row">

        <div v-if="logs != ''" class="col-md-12">

            <div class="accordion" id="logsAccordion">
                <div v-for="(log, key, i) in logs" class="accordion-item">
                    <h2 @click="toggleCollapse(i)" class="accordion-header" :id="'heading' + i">
                        <button class="accordion-button" type="button" :aria-expanded="i == 0" :aria-controls="'collapse' + i">
                            {{ key }}
                        </button>
                    </h2>
                    <div :id="'collapse' + i" class="accordion-collapse collapse" :class="i == 0 ? 'show' : ''" data-bs-parent="#logsAccordion">
                        <div class="accordion-body">
                            <ul v-for="l in log" class="list-group">
                                <li v-if="l.message != ''" class="list-group-item"><small class="text-muted">({{ l.datetime }})</small> {{l.message}}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</template>
<style scoped>
li{
    overflow-wrap: break-word;
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
        toggleCollapse: function (i){
            $("#logsAccordion #collapse" + i).collapse('toggle');
        },

        fetchData: function(){
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
        },

    },
    mounted() {
        this.fetchData();
    }
}

</script>
