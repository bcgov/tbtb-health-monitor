<template>
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="list-group" role="tablist" aria-orientation="vertical">
                <a @click="switchActiveTab(0)" href="#v-pills-sabc" class="list-group-item list-group-item-action" :class="activeTab === 0 ? 'active' : ''" id="v-pills-sabc-tab" data-bs-toggle="pill" data-bs-target="#v-pills-sabc" type="button" role="tab" aria-controls="v-pills-sabc" aria-selected="true">SABC</a>
                <a @click="switchActiveTab(1)" href="#v-pills-ptib" class="list-group-item list-group-item-action" :class="activeTab === 1 ? 'active' : ''" id="v-pills-ptib-tab" data-bs-toggle="pill" data-bs-target="#v-pills-ptib" type="button" role="tab" aria-controls="v-pills-ptib" aria-selected="false">PTIB</a>
                <a @click="switchActiveTab(2)" href="#v-pills-jira" class="list-group-item list-group-item-action" :class="activeTab === 2 ? 'active' : ''" id="v-pills-jira-tab" data-bs-toggle="pill" data-bs-target="#v-pills-jira" type="button" role="tab" aria-controls="v-pills-jira" aria-selected="false">JIRA</a>
<!--                <a @click="switchActiveTab(3)" href="#v-pills-sharepoint" class="list-group-item list-group-item-action" :class="activeTab === 3 ? 'active' : ''" id="v-pills-sharepoint-tab" data-bs-toggle="pill" data-bs-target="#v-pills-sharepoint" type="button" role="tab" aria-controls="v-pills-sharepoint" aria-selected="false">SHAREPOINT</a>-->
                <a @click="switchActiveTab(4)" href="#v-pills-wdst" class="list-group-item list-group-item-action" :class="activeTab === 4 ? 'active' : ''" id="v-pills-wdst-tab" data-bs-toggle="pill" data-bs-target="#v-pills-wdst" type="button" role="tab" aria-controls="v-pills-wdst" aria-selected="false">WDST</a>
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
        envList: '',
        activeTab: 0,
    }),
    props: [],
    methods: {
        switchActiveTab: function (tabIndex){
            this.activeTab = tabIndex;
        },

    },
    mounted: function () {

    },
}

</script>
