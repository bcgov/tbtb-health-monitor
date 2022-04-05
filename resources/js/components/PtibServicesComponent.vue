<template>
    <div class="row">
        <div class="col-12">
            <h1 v-if="envList === ''">Loading</h1>
            <template v-else>
                <div v-for="env in envList.env" class="card mb-3">
                    <div class="card-header bg-primary text-white text-uppercase">{{ env.name }}</div>
                    <div class="card-body">
                        <ul class="list-group">

                            <li v-for="(value, test) in env.cases" class="list-group-item">
                                <button @click="showService(value)" type="button" class="btn btn-link">{{ test }}</button>

                                <svg v-if="value.mute === true" @click="muteService(env.name, value)" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 106.09 122.88" style="enable-background:new 0 0 106.09 122.88;width: 14px;fill: red;float: right;position: relative;top: 4px;cursor: pointer;" xml:space="preserve" data-bs-toggle="tooltip" data-bs-placement="top" title="unmute"><g><path d="M11.3,89.02c2.07,1.25,2.74,3.94,1.49,6.01c-1.25,2.07-3.94,2.74-6.01,1.49c-2.05-1.24-3.77-3-4.96-5.1 C0.66,89.38,0,87.03,0,84.56V43.02c0-3.85,1.57-7.34,4.1-9.87c2.53-2.53,6.02-4.1,9.87-4.1h32.4L73.96,1.29 c1.7-1.71,4.47-1.72,6.18-0.02c0.86,0.85,1.29,1.98,1.29,3.1h0.01v22.75c0,2.42-1.96,4.39-4.39,4.39c-2.42,0-4.39-1.96-4.39-4.39 V14.99l-21.16,21.3c-0.8,0.94-2,1.54-3.33,1.54h-34.2c-1.43,0-2.73,0.59-3.67,1.53c-0.94,0.94-1.53,2.24-1.53,3.67v41.54 c0,0.94,0.24,1.8,0.66,2.54C9.87,87.89,10.52,88.55,11.3,89.02L11.3,89.02z M14.21,118.77c-1.71,1.71-4.49,1.71-6.2,0 s-1.71-4.49,0-6.2L98.6,21.98c1.71-1.71,4.49-1.71,6.2,0s1.71,4.49,0,6.2L14.21,118.77L14.21,118.77z M72.67,72.31 c0-2.42,1.96-4.39,4.39-4.39c2.42,0,4.39,1.96,4.39,4.39v46.18c0,2.42-1.96,4.39-4.39,4.39c-1.18,0-2.24-0.46-3.03-1.21 l-22.86-18.62c-1.87-1.52-2.16-4.28-0.63-6.15c1.52-1.87,4.28-2.16,6.15-0.63l15.99,13.02V72.31L72.67,72.31z"></path></g></svg>
                                <svg v-if="value.mute === false" @click="muteService(env.name, value)" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 122.88 92.19" style="enable-background:new 0 0 122.88 92.19;width: 20px;fill: green;float: right;position: relative;top: 4px;cursor: pointer;" xml:space="preserve" data-bs-toggle="tooltip" data-bs-placement="top" title="mute"><g><path d="M78.37,9.74c-1.81,0-3.29-1.47-3.29-3.29c0-1.81,1.47-3.29,3.29-3.29c12.29,0,23.42,4.98,31.47,13.04 c8.06,8.06,13.04,19.18,13.04,31.47c0,12.29-4.98,23.42-13.04,31.47c-8.06,8.06-19.18,13.04-31.47,13.04 c-1.81,0-3.29-1.47-3.29-3.29s1.47-3.29,3.29-3.29c10.48,0,19.96-4.25,26.83-11.11c6.87-6.87,11.11-16.35,11.11-26.83 s-4.25-19.96-11.11-26.83C98.33,13.98,88.84,9.74,78.37,9.74L78.37,9.74z M10.47,21.76h24.27L55.4,0.97 c1.28-1.28,3.35-1.29,4.63-0.01C60.68,1.59,61,2.43,61,3.28h0.01v85.49c0,1.81-1.47,3.29-3.29,3.29c-0.92,0-1.75-0.38-2.35-0.99 L34.91,73.81H10.47c-2.88,0-5.5-1.18-7.39-3.07C1.18,68.85,0,66.23,0,63.35V32.23c0-2.88,1.18-5.5,3.07-7.39 C4.97,22.94,7.59,21.76,10.47,21.76L10.47,21.76z M36.09,28.33H10.47c-1.07,0-2.04,0.44-2.75,1.15s-1.15,1.68-1.15,2.75v31.12 c0,1.07,0.44,2.04,1.15,2.75c0.71,0.71,1.68,1.15,2.75,1.15h25.62v0.02c0.74,0,1.49,0.25,2.1,0.77l16.25,13.69V11.23L38.59,27.18 C37.98,27.89,37.09,28.33,36.09,28.33L36.09,28.33z M76.71,28.52c-1.5,0-2.72-1.22-2.72-2.72c0-1.5,1.22-2.72,2.72-2.72 c6.98,0,13.3,2.83,17.87,7.4c4.57,4.57,7.4,10.89,7.4,17.87c0,6.98-2.83,13.3-7.4,17.87c-4.57,4.57-10.89,7.4-17.87,7.4 c-1.5,0-2.72-1.22-2.72-2.72c0-1.5,1.22-2.72,2.72-2.72c5.48,0,10.44-2.22,14.03-5.81c3.59-3.59,5.81-8.55,5.81-14.03 c0-5.48-2.22-10.44-5.81-14.03C87.14,30.74,82.18,28.52,76.71,28.52L76.71,28.52z"></path></g></svg>
                                <div class="form-check form-switch float-end">
                                    <input @click="pauseService(env.name, value)" class="form-check-input" type="checkbox" role="switch" :checked="value.paused == false">
                                </div>

                                <!-- if the response failed (500) and an error message set then show it -->
                                <div v-if="value.paused == false && value.status === 'Fail' && value.response != false" class="alert alert-danger" :class="value.expanded != undefined && value.expanded == true ? 'expanded' : 'collapsed'">
                                    <template v-if="value.response.length > 250">
                                        <strong v-if="value.expanded == true" class="float-end text-primary" @click="toggleAlert(1, value)">Read less</strong>
                                        <strong v-else class="float-end text-primary" @click="toggleAlert(0, value)">Read more</strong>
                                    </template>
                                    {{value.response}}</div>
                            </li>

                        </ul>
                    </div>
                </div>
            </template>
            <show-service-modal v-if="serv != ''" :servp="serv" @update="fetchData" @close="clearService"></show-service-modal>


        </div>
    </div>
</template>
<style scoped>
.list-group-item small.retest{
    visibility: hidden;
    cursor: pointer;
}
.list-group-item:hover small.retest{
    visibility: visible;
}
.alert.alert-danger.collapsed {
    height: 90px;
    overflow: hidden;
}
.alert.alert-danger strong{
    cursor: pointer;
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
        envList: '',
        serv: '',
    }),
    props: [],
    methods: {
        showService: function(serv){
            this.serv = serv;
        },
        clearService: function (){
            this.serv = '';
        },

        toggleAlert: function (state, val){
            if(state == 0)
                val.expanded = true;
            else val.expanded = false;
        },
        pauseService: function (env, test){
            let vm = this;
            let url = test.paused == true ? '/unpause-test/' + test.id : '/pause-test/' + test.id;
            test.paused = !test.paused;
            //test.status = 'Pending';
            axios({
                url: url,
                //data: formData,
                method: 'get',
                //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                headers: {'Accept': 'application/json'}
            })

                //axios.get( '/fetch-dashboard' )
                .then(function (response) {
                    // console.log('ENVLIST IS EMPTY 0');
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        muteService: function (env, test){
            let vm = this;
            let url = test.mute == true ? '/unmute-test/' + test.id : '/mute-test/' + test.id;
            test.mute = !test.mute;
            //test.status = 'Pending';
            axios({
                url: url,
                //data: formData,
                method: 'get',
                //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                headers: {'Accept': 'application/json'}
            })

                //axios.get( '/fetch-dashboard' )
                .then(function (response) {
                    // console.log('ENVLIST IS EMPTY 0');
                })
                .catch(function (error) {
                    console.log(error);
                });
        },

        fetchData: function(){
            let vm = this;
            axios({
                url: '/fetch-ptib-tests',
                //data: formData,
                method: 'get',
                //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                headers: {'Accept': 'application/json'}
            })

                //axios.get( '/fetch-dashboard' )
                .then(function (response) {
                    vm.envList = response.data.tests;
                    document.refreshTooltips();
                })
                .catch(function (error) {
                    console.log(error);
                });
        },

    },
    computed: {

    },
    created() {

    },
    mounted: function () {
        this.fetchData();
        // document.title = "StudentAidBC - Applicant Overview Info";

    },
    watch: {
    }

}

</script>
