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
        servToAddEnv: '',
    }),
    props: ['group'],
    methods: {
        showService: function(serv){
            this.serv = serv;
        },
        clearService: function (){
            this.serv = '';
            this.servToAddEnv = '';
        },
        toggleAlert: function (state, val){
            if(state == 0)
                val.expanded = true;
            else val.expanded = false;
        },
        pauseService: function (env, test){
            let url = test.paused == true ? '/unpause-test/' + test.id : '/pause-test/' + test.id;
            test.paused = !test.paused;
            axios({
                url: url,
                method: 'get',
                headers: {'Accept': 'application/json'}
            })

                .then(function (response) {
                    // console.log('ENVLIST IS EMPTY 0');
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        muteService: function (env, test){
            let url = test.mute == true ? '/unmute-test/' + test.id : '/mute-test/' + test.id;
            test.mute = !test.mute;
            //test.status = 'Pending';
            axios({
                url: url,
                method: 'get',
                headers: {'Accept': 'application/json'}
            })

                .then(function (response) {
                    // console.log('ENVLIST IS EMPTY 0');
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
                    document.refreshTooltips();
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        addService: function (env){
            this.servToAddEnv = env;
        }

    }
}
