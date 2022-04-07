<template>
    <div class="row">
        <div class="col-12">
            <h1 v-if="envList === ''">Loading</h1>
            <template v-else>
                <div v-for="env in envList.env" class="card mb-3">
                    <div class="card-header" :class="headerClass(env.name)">{{ env.name }}<small class="float-end">last tested: {{env.last_test | cleanLastTestRun}}</small></div>
                    <div class="card-body">
                        <ul class="list-group">

                            <li v-for="(value, test) in env.cases" class="list-group-item">
                                {{ test }}

                                <!-- Initial state. Loading -->
                                <span v-if="value.paused == false && (value.status === 0 || value.status === 'Pending')" class="badge bg-warning rounded-pill float-end">&nbsp;</span>
                                <span v-if="value.paused == false && (value.status === 'Pass' && value.response !== false)" class="badge bg-success rounded-pill float-end">&nbsp;</span>

                                <!-- this case will occur only for HTML pages. The html response could be 200 but fails to fetch specific text on a page -->
                                <span v-if="value.paused == false && value.status === 'Pass' && value.response === false" class="badge bg-danger rounded-pill float-end">&nbsp;</span>
                                <span v-if="value.paused == false && value.status === 'Fail'" class="badge bg-danger rounded-pill float-end">&nbsp;</span>
                                <span v-if="value.paused == true" class="badge bg-primary rounded-pill float-end">&nbsp;</span>

                                <small v-if="value.paused == false && userAuth != false" @click="reTest(env.name, value, envList.branch)" class="float-end retest pe-2 text-primary">re-test</small>

                                <!-- if the response failed (500) and an error message set then show it -->
                                <div v-if="value.status === 'Fail' && value.response != false" class="alert alert-danger" :class="value.expanded != undefined && value.expanded == true ? 'expanded' : 'collapsed'">
                                    <template v-if="value.response != null && value.response.length > 250">
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
import shared from '../home_mixin';

export default {
    mixins: [shared],
    mounted: function () {
        this.fetchData('jira');
    },
    beforeDestroy() {
        this.clearTimeout(this.timeoutVar);
    }
}


</script>
