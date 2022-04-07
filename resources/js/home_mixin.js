import axios from "axios";

export default {
    filters: {
        cleanLastTestRun: function (test){
            if(test == null) return '';
            let d = test.split('T');
            if(d.length == 1) return d[0];
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
        headerClass: function(env){
            switch (env) {
                case 'DEV':
                case 'UAT': return 'bg-light';
                default: return 'bg-primary text-white';
            }
        },
        toggleAlert: function (state, val){
            if(state == 0)
                val.expanded = true;
            else val.expanded = false;
        },
        reTest: function (env, test, branch){
            let vm = this;
            test.status = 'Pending';
            axios({
                url: '/rerun-single-test/' + branch + '/' + env + '/' + test.cmd,
                method: 'get',
                headers: {'Accept': 'application/json'}
            })

                .then(function (response) {
                    vm.fetchSingleTest(env, test, vm.envList.branch);
                    // console.log('ENVLIST IS EMPTY 0');
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        fetchSingleTest: function (env, test, branch){
            let vm = this;
            axios({
                url: '/fetch-single-test/' + branch + '/' + env + '/' + test.cmd,
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
        fetchData: function(branch){
            let vm = this;
            axios({
                url: '/fetch-tests?group=' + branch,
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
                        vm.fetchData(branch);
                    }, 30000); //every 30s
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
    },

}
