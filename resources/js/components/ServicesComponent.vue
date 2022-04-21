<template>
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="list-group" role="tablist" aria-orientation="vertical">
                <a @click="switchActiveTab(0)" href="#v-pills-sabc" class="list-group-item list-group-item-action" :class="activeTab === 0 ? 'active' : ''" id="v-pills-sabc-tab" data-bs-toggle="pill" data-bs-target="#v-pills-sabc" type="button" role="tab" aria-controls="v-pills-sabc" aria-selected="true">SABC
                    <svg v-if="activeTab === 0" @click="addService" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 32 32" style="enable-background:new 0 0 32 32;width: 20px;height: 20px;" xml:space="preserve" class="float-end">
                        <g>
                            <g id="plus_x5F_alt">
                                <path d="M16,0C7.164,0,0,7.164,0,16s7.164,16,16,16s16-7.164,16-16S24.836,0,16,0z M24,18h-6v6h-4v-6H8v-4    h6V8h4v6h6V18z"></path>
                            </g>
                        </g>
                    </svg>
                </a>
                <a @click="switchActiveTab(1)" href="#v-pills-ptib" class="list-group-item list-group-item-action" :class="activeTab === 1 ? 'active' : ''" id="v-pills-ptib-tab" data-bs-toggle="pill" data-bs-target="#v-pills-ptib" type="button" role="tab" aria-controls="v-pills-ptib" aria-selected="false">PTIB
                    <svg v-if="activeTab === 1" @click="addService" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 32 32" style="enable-background:new 0 0 32 32;width: 20px;height: 20px;" xml:space="preserve" class="float-end">
                        <g>
                            <g id="plus_x5F_alt">
                                <path d="M16,0C7.164,0,0,7.164,0,16s7.164,16,16,16s16-7.164,16-16S24.836,0,16,0z M24,18h-6v6h-4v-6H8v-4    h6V8h4v6h6V18z"></path>
                            </g>
                        </g>
                    </svg>

                </a>
                <a @click="switchActiveTab(2)" href="#v-pills-jira" class="list-group-item list-group-item-action" :class="activeTab === 2 ? 'active' : ''" id="v-pills-jira-tab" data-bs-toggle="pill" data-bs-target="#v-pills-jira" type="button" role="tab" aria-controls="v-pills-jira" aria-selected="false">JIRA
                    <svg v-if="activeTab === 2" @click="addService" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 32 32" style="enable-background:new 0 0 32 32;width: 20px;height: 20px;" xml:space="preserve" class="float-end">
                        <g>
                            <g id="plus_x5F_alt">
                                <path d="M16,0C7.164,0,0,7.164,0,16s7.164,16,16,16s16-7.164,16-16S24.836,0,16,0z M24,18h-6v6h-4v-6H8v-4    h6V8h4v6h6V18z"></path>
                            </g>
                        </g>
                    </svg>
                </a>

                <a @click="switchActiveTab(4)" href="#v-pills-wdst" class="list-group-item list-group-item-action" :class="activeTab === 4 ? 'active' : ''" id="v-pills-wdst-tab" data-bs-toggle="pill" data-bs-target="#v-pills-wdst" type="button" role="tab" aria-controls="v-pills-wdst" aria-selected="false">WDST
                    <svg v-if="activeTab === 4" @click="addService" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 32 32" style="enable-background:new 0 0 32 32;width: 20px;height: 20px;" xml:space="preserve" class="float-end">
                        <g>
                            <g id="plus_x5F_alt">
                                <path d="M16,0C7.164,0,0,7.164,0,16s7.164,16,16,16s16-7.164,16-16S24.836,0,16,0z M24,18h-6v6h-4v-6H8v-4    h6V8h4v6h6V18z"></path>
                            </g>
                        </g>
                    </svg>
                </a>

                <a @click="switchActiveTab(5)" href="#v-pills-infrastructure" class="list-group-item list-group-item-action" :class="activeTab === 5 ? 'active' : ''" id="v-pills-infrastructure-tab" data-bs-toggle="pill" data-bs-target="#v-pills-infrastructure" type="button" role="tab" aria-controls="v-pills-infrastructure" aria-selected="false">INFRASTRUCTURE
                    <svg v-if="activeTab === 5" @click="addService" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 32 32" style="enable-background:new 0 0 32 32;width: 20px;height: 20px;" xml:space="preserve" class="float-end">
                        <g>
                            <g id="plus_x5F_alt">
                                <path d="M16,0C7.164,0,0,7.164,0,16s7.164,16,16,16s16-7.164,16-16S24.836,0,16,0z M24,18h-6v6h-4v-6H8v-4    h6V8h4v6h6V18z"></path>
                            </g>
                        </g>
                    </svg>
                </a>
            </div>
        </div>
        <div class="col-md-9">
            <div class="tab-content" id="v-pills-tabContent">
                <div v-if="activeTab === 0" class="tab-pane fade show active" id="v-pills-sabc" role="tabpanel" aria-labelledby="v-pills-sabc-tab">
                    <services-group-component :group="'sabc'"></services-group-component>
                </div>
                <div v-if="activeTab === 1" class="tab-pane fade show active" id="v-pills-ptib" role="tabpanel" aria-labelledby="v-pills-ptib-tab">
                    <services-group-component :group="'ptib'"></services-group-component>
                </div>
                <div v-if="activeTab === 2" class="tab-pane fade show active" id="v-pills-jira" role="tabpanel" aria-labelledby="v-pills-jira-tab">
                    <services-group-component :group="'jira'"></services-group-component>
                </div>
                <div v-if="activeTab === 3" class="tab-pane fade show active" id="v-pills-sharepoint" role="tabpanel" aria-labelledby="v-pills-sharepoint-tab">
                    <services-group-component :group="'sharepoint'"></services-group-component>
                </div>
                <div v-if="activeTab === 4" class="tab-pane fade show active" id="v-pills-wdst" role="tabpanel" aria-labelledby="v-pills-wdst-tab">
                    <services-group-component :group="'wdst'"></services-group-component>
                </div>
                <div v-if="activeTab === 5" class="tab-pane fade show active" id="v-pills-infrastructure" role="tabpanel" aria-labelledby="v-pills-infrastructure-tab">
                    <services-group-component :group="'infrastructure'"></services-group-component>
                </div>
            </div>
        </div>
        <add-service-modal v-if="branch != ''" :branch="branch" @update="refresh" @close="clearService"></add-service-modal>

    </div>
</template>
<style scoped>
svg{
    cursor: pointer;
}
svg path{
    fill: #fff;
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
        branch: '',
        activeTab: 0,
    }),
    props: [],
    methods: {
        switchActiveTab: function (tabIndex){
            this.activeTab = tabIndex;
        },
        refresh: function (){
            var a = this.activeTab;
            let vm = this;
            this.activeTab = -1;
            setTimeout(function (){
                vm.activeTab = a;
            }, 500);
        },
        addService: function (){
            switch (this.activeTab) {
                case 0: this.branch = 'SABC'; break;
                case 1: this.branch = 'PTIB'; break;
                case 2: this.branch = 'JIRA'; break;
                case 3: this.branch = 'SHAREPOINT'; break;
                case 4: this.branch = 'WDST'; break;
                case 5: this.branch = 'INFRASTRUCTURE'; break;
                default: this.branch = '';
            }
        },
        clearService: function (){
            this.branch = '';
        },

    },
}

</script>
