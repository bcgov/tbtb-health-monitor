<template>
    <div class="row">
        <div class="col-12">
            <h1 v-if="envList === ''">Loading</h1>
            <template v-else>
                <div v-for="env in envList.env" class="card mb-3">
                    <div class="card-header bg-primary text-white text-uppercase">{{ env.name }}<small class="float-end">last tested: {{env.last_test | cleanLastTestRun}}</small></div>
                    <div class="card-body">
                        <ul class="list-group">

                            <li v-for="(value, test) in env.cases" class="list-group-item">
                                {{ test }}
<!--                                <i v-if="updateLastTestRun(value)"></i>-->
                                <!-- Initial state. Loading -->
                                <span v-if="value.paused == false && (value.status === 0 || value.status === 'Pending')" class="badge bg-warning rounded-pill float-end">&nbsp;</span>
                                <span v-if="value.paused == false && (value.status === 'Pass' && value.response !== false)" class="badge bg-success rounded-pill float-end">&nbsp;</span>

                                <!-- this case will occur only for HTML pages. The html response could be 200 but fails to fetch specific text on a page -->
                                <span v-if="value.paused == false && value.status === 'Pass' && value.response === false" class="badge bg-danger rounded-pill float-end">&nbsp;</span>
                                <span v-if="value.paused == false && value.status === 'Fail'" class="badge bg-danger rounded-pill float-end">&nbsp;</span>
                                <span v-if="value.paused == true" class="badge bg-primary rounded-pill float-end">&nbsp;</span>

                                <small v-if="value.paused == false && userAuth != false" @click="reTest(env.name, value)" class="float-end retest pe-2 text-primary">re-test</small>

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
                    <div v-if="lastUpdate !== ''" class="card-footer"><small>last refresh: {{lastUpdate}}</small></div>
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
        cleanLastTestRun: function (test){
            if(test == '') return '';
            let d = test.split('T');
            let t = d[1].split('.');
            return d[0] + " " + t[0];
        },
        formatAppNumber: function(value){
            let year = value.slice(0, 4);
            let extra = value.slice(4);

            return year + '-' + extra;
        }
    },

    data: () => ({
        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        envList: '',
        lastUpdate: '',
        userAuth: false,
        timeoutVar: '',
        lastTestRun: '',
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
                method: 'get',
                headers: {'Accept': 'application/json'}
            })

                .then(function (response) {
                    vm.fetchSingleTest(env, test);
                    // console.log('ENVLIST IS EMPTY 0');
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        fetchSingleTest: function (env, test){
            let vm = this;
            axios({
                url: '/fetch-single-test/sabc/' + env + '/' + test.cmd,
                method: 'get',
                headers: {'Accept': 'application/json'}
            })

                .then(function (response) {
                    test.status = response.data.status;
                    test.response = response.data.response;
                    // console.log('ENVLIST IS EMPTY 1');
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        fetchData: function(){
            let vm = this;
            axios({
                url: '/fetch-sabc-tests',
                method: 'get',
                headers: {'Accept': 'application/json'}
            })
                .then(function (response) {
                    vm.envList = response.data.tests;
                    vm.userAuth = response.data.user_auth;
                    let today = new Date();
                    let date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                    let time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
                    let dateTime = date+' '+time;
                    vm.lastUpdate = dateTime;
                    vm.timeoutVar = setTimeout(function (){
                        vm.fetchData();
                    }, 30000); //every 30s
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
    },
    mounted: function () {
        this.fetchData();
    },
    beforeDestroy() {
        clearTimeout(this.timeoutVar);
    }

}

</script>
