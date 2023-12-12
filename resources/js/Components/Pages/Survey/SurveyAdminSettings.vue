<template>
    <div class="card bg-white shadow-md flex-grow p-4">
        <div class="card-header">Settings</div>
        <div class="card-body">
            <span>{{ surveyStore.settings }}</span>
            <div class="form-control">
                <label class="cursor-pointer label">
                    <span class="label-text">Make survey timed</span>
                    <input
                        type="checkbox"
                        v-model="surveyStore.settings.is_timed"
                        class="toggle tog checkbox-accent"
                        @change="saveSetting('is_timed')"
                        :checked="surveyStore.settings.is_timed" />
                </label>
                <label class="cursor-pointer label" v-if="surveyStore.settings.is_timed">
                    <span class="label-text">The the duration </span><input type="time" class="input input-ghost">
                </label>
            </div>
            <div class="form-control">
                <label class="label cursor-pointer">
                    <span class="label-text">Remember me</span>
                    <input type="checkbox" class="toggle" checked />
                </label>
            </div>
        </div>
    </div>
</template>

<script>
import {useSurveyStore} from "@/Stores/SurveyStore.js";
import { onMounted } from "vue";

export default {
    name: "SurveyAdminSettings",
    props: ['settings'],
    setup(props){
        const surveyStore = useSurveyStore();

        const surveyId = surveyStore.newSurvey.survey_id;

        const saveSetting = async (key) => {
            try {
                const response = await axios.post(route('survey.setting.save'), {
                    survey_id: surveyId,
                    key: key,
                    value: surveyStore.settings[key],
                });
                console.log(response.data.message); // Log success message
            } catch (error) {
                console.error('Error:', error.message || error);
            }
        };

        onMounted(() => {
            let setting_keys = Object.keys(props.settings)
            console.log(setting_keys)
            setting_keys.forEach((key) => {
                alert(key)
                console.log(surveyStore.settings)
                surveyStore.settings[key] = props.setting[key];
            })
        })

        return {
            surveyStore, surveyId, saveSetting
        }
    }
}
</script>

<style scoped>
input[type=time]::-webkit-datetime-edit-ampm-field {
    display: none;
}
</style>
