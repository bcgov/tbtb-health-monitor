<template>
    <div class="row">
        <div class="col-3">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white text-uppercase">Add New User</div>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <input type="name" class="form-control" :class="frm.name.valid === true ? '' : 'is-invalid'" placeholder="Name" v-model="frm.name.txt" :disabled="formSubmitting == true">
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" :class="frm.email.valid === true ? '' : 'is-invalid'" placeholder="Email" v-model="frm.email.txt" :disabled="formSubmitting == true">
                        </div>
                        <div class="mb-3">
                            <input type="number" class="form-control" :class="frm.cell.valid === true ? '' : 'is-invalid'" placeholder="Cell number" v-model="frm.cell.txt" :disabled="formSubmitting == true">
                        </div>
                        <div class="mb-3">
                            <select class="form-select" :class="frm.lvl.valid === true ? '' : 'is-invalid'" v-model="frm.lvl.txt" :disabled="formSubmitting == true">
                                <option value="">Escalation Level</option>
                                <option value="developer">Developer (lvl 1)</option>
                                <option value="support">Tech Support (lvl 2)</option>
                                <option value="management">Management (lvl 3)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button @click="validateForm" class="btn btn-primary" type="button">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-9">
            <h1 v-if="accounts === []">Loading</h1>
            <template v-else>
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white text-uppercase">contacts<button @click="sendMsg" type="button" class="btn btn-link btn-sm float-end text-white">Send Message</button></div>
                        <div class="card-body">
                            <table class="table" aria-label="accounts table">
                                <thead>
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Cell Number</th>
                                        <th scope="col">Level</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">SMS Enabled</th>
                                        <th scope="col">Email Enabled</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="contact in accounts">
                                        <th scope="row">{{ contact.name }}</th>
                                        <td>{{ contact.email }}</td>
                                        <td>{{ contact.cell_number }}</td>
                                        <td>{{ contact.level }}</td>
                                        <td>{{ contact.status }}</td>
                                        <td>{{ contact.sms_enabled }}</td>
                                        <td>{{ contact.email_enabled }}</td>
                                        <td><button @click="showContact(contact)" type="button" class="btn btn-sm btn-info">edit</button></td>
                                    </tr>

                                </tbody>
                            </table>

                        </div>
                    </div>
            </template>
        </div>

        <div class="modal fade in" id="showContact" tabindex="-1" role="dialog" aria-hidden="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <form v-if="contact != ''">
                            <div class="mb-3">
                                <input type="name" class="form-control" :class="contact.name !== '' ? '' : 'is-invalid'" placeholder="Name" v-model="contact.name" :disabled="formSubmitting == true">
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" :class="contact.email !== '' ? '' : 'is-invalid'" placeholder="Email" v-model="contact.email" :disabled="formSubmitting == true">
                            </div>
                            <div class="mb-3">
                                <input type="number" class="form-control" :class="contact.cell_number !== '' ? '' : 'is-invalid'" placeholder="Cell number" v-model="contact.cell_number" :disabled="formSubmitting == true">
                            </div>
                            <div class="mb-3">
                                <select class="form-select" :class="contact.level !== '' ? '' : 'is-invalid'" v-model="contact.level" :disabled="formSubmitting == true">
                                    <option value="">Escalation Level</option>
                                    <option value="developer">Developer (lvl 1)</option>
                                    <option value="support">Tech Support (lvl 2)</option>
                                    <option value="management">Management (lvl 3)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <select class="form-select" :class="contact.status !== '' ? '' : 'is-invalid'" v-model="contact.status" :disabled="formSubmitting == true">
                                    <option value="">Choose status</option>
                                    <option value="active">Active</option>
                                    <option value="disabled">Disabled</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <select class="form-select" :class="contact.sms_enabled !== '' ? '' : 'is-invalid'" v-model="contact.sms_enabled" :disabled="formSubmitting == true">
                                    <option value="">Enable / Disable Sending SMS</option>
                                    <option value="true">True</option>
                                    <option value="false">False</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <select class="form-select" :class="contact.email_enabled !== '' ? '' : 'is-invalid'" v-model="contact.email_enabled" :disabled="formSubmitting == true">
                                    <option value="">Enable / Disable Sending Emails</option>
                                    <option value="true">True</option>
                                    <option value="false">False</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button @click="validateForm2()" class="btn btn-success" type="button">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade in" id="sendMsg" tabindex="-1" role="dialog" aria-hidden="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <form>
                            <div class="mb-3">
                                <label for="msgContact" class="form-label">Select Account</label>
                                <select id="msgContact" class="form-select" v-model="msg.contactIndex" :disabled="formSubmitting == true">
                                    <option v-for="(account,i) in accounts" :value="i">{{ account.name }}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="msgBody" class="form-label">Message</label>
                                <textarea id="msgBody" class="form-control" :class="msg.body !== '' ? '' : 'is-invalid'" placeholder="Message body" v-model="msg.body" :disabled="formSubmitting == true"></textarea>
                            </div>

                            <div class="col-12">
                                <button @click="validateForm3()" class="btn btn-success" type="button">Send Message</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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
            'name': {'txt': '', 'required': true, 'valid': true},
            'email': {'txt': '', 'required': true, 'valid': true},
            'cell': {'txt': '', 'required': true, 'valid': true},
            'lvl': {'txt': '', 'required': true, 'valid': true},
            'service': {'txt': '', 'required': true, 'valid': true},
        },
        msg: { contactIndex: '', body: '' },
        accounts: [],
        formSubmitting: false,
        contact: ''
    }),
    props: [],
    methods: {
        showContact: function(con){
            this.contact = con;
            $('#showContact').modal('toggle');
        },
        sendMsg: function (){
            $('#sendMsg').modal('toggle');
        },
        validateForm3: function (){
            this.msg.body = this.msg.body.trim();

            if(this.msg.body == '' || this.msg.contactIndex == ''){
                return false;
            }
            this.submitMsg();
        },
        validateForm2: function (){
            this.contact.name = this.contact.name.trim();
            this.contact.email = this.contact.email.trim();
            this.contact.cell_number = this.contact.cell_number.replace ( /[^0-9]/g, '' );

            if(this.contact.name == '' || this.contact.email == '' || this.contact.cell_number == ''){
                return false;
            }
            this.updateContact();
        },
        validateForm: function (){
            this.frm.name.txt = this.frm.name.txt.trim();
            if(this.frm.name.txt === ''){
                this.frm.name.valid = false;
                return false;
            }
            this.frm.name.valid = true;

            this.frm.email.txt = this.frm.email.txt.trim();
            if(this.frm.email.txt === '' || this.frm.email.txt.search(/.+@.+\..+/) !== 0){
                this.frm.email.valid = false;
                return false;
            }
            this.frm.email.valid = true;

            this.frm.cell.txt = this.frm.cell.txt.replace ( /[^0-9]/g, '' );
            if(this.frm.cell.txt === ''){
                this.frm.cell.valid = false;
                return false;
            }
            this.frm.cell.valid = true;

            if(this.frm.lvl.txt === ''){
                this.frm.lvl.valid = false;
                return false;
            }
            this.frm.lvl.valid = true;

            this.submitForm();
        },
        submitMsg: function (){
            let vm = this;
            vm.formSubmitting = true;

            let formData = new FormData();
            formData.append('msg', this.msg.body);
            formData.append('contact', this.accounts[this.msg.contactIndex].id);
            axios({
                url: '/send-message',
                data: formData,
                method: 'post',
                headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
            })
                .then(function (response) {
                    vm.formSubmitting = false;
                    $('#sendMsg').modal('toggle');
                    vm.msg = { contactIndex: '', body: '' };
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        updateContact: function (){
            let vm = this;
            vm.formSubmitting = true;

            let formData = new FormData();
            formData.append('contact', JSON.stringify(this.contact));
            axios({
                url: '/update-contacts/' + this.contact.id,
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
                url: '/add-contacts',
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
                url: '/remove-contacts/' + contact.id + '/' + test.id,
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
                url: '/fetch-accounts',
                method: 'get',
                headers: {'Accept': 'application/json'}
            })
            .then(function (response) {
                vm.accounts = response.data.accounts;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
    },
    mounted: function () {
        this.fetchData();
    }
}
</script>
