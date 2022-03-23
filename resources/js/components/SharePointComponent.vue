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
                                {{ test }}

                                <!-- Initial state. Loading -->
                                <span v-if="value.status === 0 || value.status === 'Pending'" class="badge bg-warning rounded-pill float-end">&nbsp;</span>
                                <span v-if="value.status === 'Pass' && value.response !== false" class="badge bg-success rounded-pill float-end">&nbsp;</span>

                                <!-- this case will occur only for HTML pages. The html response could be 200 but fails to fetch specific text on a page -->
                                <span v-if="value.status === 'Pass' && value.response === false" class="badge bg-danger rounded-pill float-end">&nbsp;</span>
                                <span v-if="value.status === 'Fail'" class="badge bg-danger rounded-pill float-end">&nbsp;</span>

                                <small @click="reTest(env.name, value)" class="float-end retest pe-2 text-primary">re-test</small>

                                <!-- if the response failed (500) and an error message set then show it -->
                                <div v-if="value.status === 'Fail' && value.response != false" class="alert alert-danger" :class="value.expanded != undefined && value.expanded == true ? 'expanded' : 'collapsed'">
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
    }),
    props: [],
    methods: {
        toggleAlert: function (state, val){
          if(state == 0)
              val.expanded = true;
          else val.expanded = false;
        },
        reTest: function (env, test){
            let vm = this;
            test.status = 'Pending';
            axios({
                url: '/rerun-single-test/sabc/' + env + '/' + test.cmd,
                //data: formData,
                method: 'get',
                //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                headers: {'Accept': 'application/json'}
            })

                //axios.get( '/fetch-dashboard' )
                .then(function (response) {
                    // vm.envList = response.data.tests;
                    vm.fetchSingleTest(env, test);
                    console.log('ENVLIST IS EMPTY 0');
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        fetchSingleTest: function (env, test){
            let vm = this;
            axios({
                url: '/fetch-single-test/sabc/' + env + '/' + test.cmd,
                //data: formData,
                method: 'get',
                //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                headers: {'Accept': 'application/json'}
            })

                //axios.get( '/fetch-dashboard' )
                .then(function (response) {
                    test.status = response.data.status;
                    test.response = response.data.response;
                    console.log('ENVLIST IS EMPTY 1');
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        fetchData: function(){
            let vm = this;
            axios({
                url: '/fetch-sabc-tests',
                //data: formData,
                method: 'get',
                //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                headers: {'Accept': 'application/json'}
            })

                //axios.get( '/fetch-dashboard' )
                .then(function (response) {
                    vm.envList = response.data.tests;
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        runTests: function (){
            if(this.envList === '') {
                return false;
            }

            for (const [key, value] of Object.entries(this.envList.env)) {
                console.log("ENV: " + `${value.name}`);
                for (const [k, v] of Object.entries(value.cases)) {
                    //console.log(`${key}: ${value}`);
                    console.log(`${k}: ${v.test_url}`);

                    let vm = this;
                    axios({
                        url: 'service-check/sabc/' + value.name + '/' + v.test_url,
                        method: 'get',
                        headers: {'Accept': 'application/json'}
                    })

                        .then(function (response) {
                            v.status = response.data.status;
                            v.data = response.data.result;
                        })
                        .catch(function (error) {
                            console.log(error);
                        });

                }
            }


        }
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
