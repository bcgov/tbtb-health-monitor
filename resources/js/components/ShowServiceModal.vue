<template>

    <div class="modal fade in" id="showService" tabindex="-1" role="dialog" aria-hidden="false" aria-labelledby="showServiceLabel" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="showServiceLabel">Update Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form v-if="serv != ''">
                        <div class="mb-3">
                            <label for="service_name" class="form-label">Service Name <em class="text-danger">*</em></label>
                            <input id="service_name" type="text" class="form-control" :class="serv.name != '' ? '' : 'is-invalid'" placeholder="Service Name" v-model="serv.name" :disabled="formSubmitting == true">
                        </div>
                        <div class="mb-3">
                            <label for="service_group" class="form-label">Service Group <em class="text-danger">*</em></label>
                            <select id="service_group" class="form-select" :class="serv.group != '' ? '' : 'is-invalid'" v-model="serv.group" :disabled="formSubmitting == true">
                                <option value="">Select Group</option>
                                <option value="SABC">SABC</option>
                                <option value="PTIB">PTIB</option>
                                <option value="JIRA">JIRA</option>
                                <option value="WDST">WDST</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="service_env" class="form-label">Environment <em class="text-danger">*</em></label>
                            <select id="service_env" class="form-select" :class="serv.env != '' ? '' : 'is-invalid'" v-model="serv.env" :disabled="formSubmitting == true">
                                <option value="">Select Environment</option>
                                <option value="production">PRODUCTION</option>
                                <option value="dev">DEV</option>
                                <option value="uat">UAT</option>
                                <option value="test">TEST</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="service_type" class="form-label">Service Type <em class="text-danger">*</em></label>
                            <select id="service_type" class="form-select" :class="serv.test_type != '' ? '' : 'is-invalid'" v-model="serv.test_type" :disabled="formSubmitting == true">
                                <option value="">Select Type</option>
                                <option value="wsdl">WSDL</option>
                                <option value="curl">cURL</option>
                                <option value="db">Database</option>
                                <option value="html">HTML</option>
                                <option value="crawl">Crawl</option>
                            </select>
                            <p v-if="serv.test_type == 'crawl'" class="alert alert-warning mt-3">Crawl services require Unit Tests being created under tests/Browser</p>
                        </div>
                        <div class="mb-3">
                            <label for="service_url" class="form-label">Service URL</label>
                            <input id="service_url" type="text" class="form-control" placeholder="Service URL" v-model="serv.url" :disabled="formSubmitting == true">
                        </div>
                        <div class="mb-3">
                            <label for="service_assert" class="form-label">Validation Text</label>
                            <input id="service_assert" type="text" class="form-control" placeholder="Service Validation Text (assert text)" v-model="serv.assert_text" :disabled="formSubmitting == true">
                        </div>
                        <div class="mb-3">
                            <label for="service_post_data" class="form-label">Extra POST Params</label>
                            <textarea id="service_post_data" class="form-control" placeholder="JSON Object to pass as param when POSTing data" v-model="serv.post_data" :disabled="formSubmitting == true">{{serv.post_data}}</textarea>
                        </div>

                        <div class="col-12">
                            <button @click="validateForm2()" class="btn btn-success" :class="formSubmitting == true ? 'disabled' : ''" type="button" :disabled="formSubmitting == true">Update</button>
                            <button @click="deleteService()" class="btn btn-outline-danger float-end" :class="formSubmitting == true ? 'disabled' : ''" type="button" :disabled="formSubmitting == true">Delete Service</button>
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
        serv: '',
        formSubmitting: false,
    }),
    props: ['servp'],
    methods: {
        deleteService: function (){
            let conf = confirm("Are you sure you want to delete this service?");
            if(conf === true){
                let vm = this;
                axios({
                    url: '/delete-services/' + this.serv.id,
                    method: 'get',
                    headers: {'Accept': 'application/json'}
                })

                    .then(function (response) {
                        $('#showService').modal('toggle');
                        vm.$emit("update", vm.serv.group);
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }
        },
        validateForm2: function (){
            this.serv.name = this.serv.name.trim();

            if(this.serv.name == null || this.serv.group == null || this.serv.env == null || this.serv.test_type == null){
                return false;
            }
            this.updateService();
        },
        updateService: function (){
            let vm = this;
            vm.formSubmitting = true;

            let formData = new FormData();
            formData.append('serv', JSON.stringify(this.serv));
            axios({
                url: '/update-services/' + this.serv.id,
                data: formData,
                method: 'post',
                headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
            })
                .then(function (response) {
                    vm.formSubmitting = false;
                    vm.serv = '';
                    $('#showService').modal('toggle');
                    vm.$emit("update", vm.serv.group);
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
    },
    mounted: function () {
        this.serv = this.servp;
        $('#showService').modal('toggle');

        let vm = this;
        jQuery("#showService").on("hidden.bs.modal", function () {
            vm.$emit("close");
        });
        jQuery("#showService").on("hidePrevented.bs.modal", function () {
            vm.$emit("close");
        });

    },
}

</script>
