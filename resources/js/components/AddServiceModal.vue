<template>

    <div class="modal fade in" id="addService" tabindex="-1" role="dialog" aria-hidden="false" aria-labelledby="addServiceLabel" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addServiceLabel">Add Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form v-if="newService != ''">
                        <div class="mb-3">
                            <label for="service_name" class="form-label">Service Name <em class="text-danger">*</em></label>
                            <input id="service_name" type="text" class="form-control" :class="newService.name.cls" placeholder="Service Name" v-model="newService.name.val" :disabled="formSubmitting == true">
                        </div>
                        <div class="mb-3">
                            <label for="service_env" class="form-label">Environment <em class="text-danger">*</em></label>
                            <select id="service_env" class="form-select" :class="newService.env.cls" v-model="newService.env.val" :disabled="formSubmitting == true">
                                <option value="">Select Environment</option>
                                <option value="production">PRODUCTION</option>
                                <option value="dev">DEV</option>
                                <option value="uat">UAT</option>
                                <option value="test">TEST</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="service_type" class="form-label">Service Type <em class="text-danger">*</em></label>
                            <select id="service_type" class="form-select" :class="newService.test_type.cls" v-model="newService.test_type.val" :disabled="formSubmitting == true">
                                <option value="">Select Type</option>
                                <option value="wsdl">WSDL</option>
                                <option value="curl">cURL</option>
                                <option value="db">Database</option>
                                <option value="html">HTML</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="service_url" class="form-label">Service URL</label>
                            <input id="service_url" type="text" class="form-control" placeholder="Service URL" v-model="newService.service_url.val" :disabled="formSubmitting == true">
                        </div>
                        <div class="mb-3">
                            <label for="service_assert" class="form-label">Validation Text</label>
                            <input id="service_assert" type="text" class="form-control" placeholder="Service Validation Text (assert text)" v-model="newService.assert_text.val" :disabled="formSubmitting == true">
                        </div>
                        <div class="mb-3">
                            <label for="service_post_data" class="form-label">Extra POST Params</label>
                            <textarea id="service_post_data" class="form-control" placeholder="JSON Object to pass as param when POSTing data" v-model="newService.post_data.val" :disabled="formSubmitting == true">{{newService.post_data.val}}</textarea>
                        </div>

                        <div class="col-12">
                            <button @click="validateForm2()" class="btn btn-success" :class="formSubmitting == true ? 'disabled' : ''" type="button" :disabled="formSubmitting == true">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</template>
<style scoped>

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
        newService: '',
        formSubmitting: false,
    }),
    props: ['branch'],
    methods: {
        resetService: function(){
            this.newService = {
                'name': {'cls': '','val':''},
                'test_type': {'cls': '','val':''},
                'service_url': {'cls': '','val':''},
                'assert_text': {'cls': '','val':''},
                'post_data': {'cls': '','val':''},
                'env': {'cls':'','val':''},
                'group': {'cls':'','val':''}
            };
        },
        validateForm2: function (){
            this.newService.name.val = this.newService.name.val.trim();
            this.newService.name.cls = this.newService.test_type.cls = '';

            if(this.newService.name.val == null){
                this.newService.name.cls = 'is-invalid';
            }
            if(this.newService.test_type.val == null){
                this.newService.test_type.cls = 'is-invalid';
            }
            if(this.newService.name.cls !== '' || this.newService.test_type.cls !== ''){
                return false;
            }
            this.updateService();
        },
        updateService: function (){
            let vm = this;
            vm.formSubmitting = true;

            let formData = new FormData();
            formData.append('serv', JSON.stringify(this.newService));
            axios({
                url: '/create-services',
                data: formData,
                method: 'post',
                headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
            })
                .then(function (response) {
                    vm.$emit("update", vm.newService.group.val);
                    vm.formSubmitting = false;
                    vm.resetService();
                    $('#addService').modal('toggle');

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
    },
    mounted: function () {
        $('#addService').modal('toggle');
        this.resetService();
        this.newService.group.val = this.branch;
        this.fetchData(this.branch);

        let vm = this;
        jQuery("#addService").on("hidden.bs.modal", function () {
            vm.$emit("close");
        });
        jQuery("#addService").on("hidePrevented.bs.modal", function () {
            vm.$emit("close");
        });

    },
}

</script>
