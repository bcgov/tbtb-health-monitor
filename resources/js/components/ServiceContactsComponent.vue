<template>
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white text-uppercase">Add User TO SERVICE</div>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <select class="form-select" :class="frm.name.valid === true ? '' : 'is-invalid'" v-model="frm.name.id" :disabled="formSubmitting == true">
                                <option value="">Select Account</option>
                                <option v-for="c in contacts" :value="c.id">{{ c.name }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <select class="form-select" :class="frm.service.valid === true ? '' : 'is-invalid'" v-model="frm.service.id" :disabled="formSubmitting == true">
                                <option value="">Service to subscribe to</option>
                                <option v-for="t in allTests" :value="t.id">{{ t.name }}</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button @click="validateForm" class="btn btn-primary" type="button">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <h1 v-if="lists === ''">Loading</h1>
            <template v-else>
                <div v-for="(envList, grop) in lists">
                    <div v-for="env in envList.env" class="card mb-3">
                        <div class="card-header bg-primary text-white text-uppercase">{{ grop }} {{ env.name }}</div>
                        <div class="card-body">
                            <ul class="list-group">

                                <li v-for="(value, test) in env.cases" class="list-group-item">
                                    {{ test }}

                                    <div class="float-end">
                                        <ul>
                                            <li v-for="contact in value.contacts">
                                                <span class="me-3">{{contact.name}}</span>
                                                <svg @click="removeContact(test, value, contact)" data-name="Layer x" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.88 122.88" style="width: 12px;height: 12px;fill: red;float: right;top: 5px;position: relative;cursor: pointer;">
                                                    <path class="cls-1" d="M6,6H6a20.53,20.53,0,0,1,29,0l26.5,26.49L87.93,6a20.54,20.54,0,0,1,29,0h0a20.53,20.53,0,0,1,0,29L90.41,61.44,116.9,87.93a20.54,20.54,0,0,1,0,29h0a20.54,20.54,0,0,1-29,0L61.44,90.41,35,116.9a20.54,20.54,0,0,1-29,0H6a20.54,20.54,0,0,1,0-29L32.47,61.44,6,34.94A20.53,20.53,0,0,1,6,6Z"></path>
                                                </svg>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
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
        frm: {
            'name': {'id': '', 'required': true, 'valid': true},
            'service': {'id': '', 'required': true, 'valid': true},
        },
        lists: '',
        allTests: [],
        contacts: [],
        formSubmitting: false,
        contact: ''
    }),
    props: [],
    methods: {
        showContact: function(con){
            this.contact = con;
            $('#showContact').modal('toggle');
        },
        validaeForm2: function (){
            this.contact.name = this.contact.name.trim();
            this.contact.email = this.contact.email.txt.trim();
            this.contact.cell_number = this.contact.cell_number.replace ( /[^0-9]/g, '' );

            if(this.contact.name == '' || this.contact.email == '' || this.contact.cell_number == ''){
                return false;
            }
            this.updateContact();

        },
        validateForm: function (){
            if(this.frm.name.id === ''){
                this.frm.name.valid = false;
                return false;
            }
            this.frm.name.valid = true;

            if(this.frm.service.id === ''){
                this.frm.service.valid = false;
                return false;
            }
            this.frm.service.valid = true;

            this.submitForm();
        },
        updateContact: function (){
            let vm = this;
            vm.formSubmitting = true;

            let formData = new FormData();
            formData.append('contact', JSON.stringify(this.contact));
            axios({
                url: '/update-service-contacts/' + this.contact.id,
                data: formData,
                method: 'post',
                headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
            })
                .then(function (response) {
                    vm.formSubmitting = false;
                    vm.contact = '';
                    $('#showContact').modal('toggle');
                    vm.fetchData();
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        submitForm: function (){
            let vm = this;
            vm.formSubmitting = true;

            let formData = new FormData();
            formData.append('frm', JSON.stringify(this.frm));
            axios({
                url: '/add-service-contacts',
                data: formData,
                method: 'post',
                headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
            })
                .then(function (response) {
                    vm.formSubmitting = false;
                    vm.fetchData();
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        removeContact: function (testName, test, contact){
            let vm = this;
            axios({
                url: '/remove-service-contacts/' + contact.id + '/' + test.id,
                method: 'get',
                headers: {'Accept': 'application/json'}
            })
                .then(function (response) {
                    vm.fetchData();
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        fetchData: function(){
            let vm = this;
            axios({
                url: '/fetch-service-contacts',
                method: 'get',
                headers: {'Accept': 'application/json'}
            })
            .then(function (response) {
                vm.lists = response.data.lists;
                vm.allTests = response.data.alltests;
                vm.contacts = response.data.contacts;
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
