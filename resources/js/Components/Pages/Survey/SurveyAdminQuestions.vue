<template>
    <div class="flex space-x-8 mb-4">
        <!-- Survey Header -->
        <div class="card bg-white shadow-md flex-grow">
            <div class="card-body">
                <div class="flex">
                    <h2 class="card-title flex-grow" v-if="!editing_survey.title" @click="editSurveyTitle">
                        {{ surveyStore.newSurvey.title ? surveyStore.newSurvey.title : 'Survey Title - (Click to Add)' }}
                    </h2>
                    <input
                        v-if="editing_survey.title"
                        v-model="surveyStore.newSurvey.title"
                        @change="storeSurvey('title')"
                        id="survey_title_field"
                        type="text"
                        placeholder="Type here and press enter"
                        class="input input-ghost flex-grow w-full max-w-xs focus:bg-gray-50"
                    />
                    <div class="flex-grow" v-if="editing_survey.title"></div>
                    <select
                        @change="storeSurvey('all')"
                        v-model="surveyStore.newSurvey.status"
                        class="select select-ghost focus:bg-gray-100 select-bordered max-w-xs"
                    >
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>

                <div v-if="!editing_survey.description" @click="editing_survey.description = true">
                    {{ surveyStore.newSurvey.description ? surveyStore.newSurvey.description : 'Survey description (Click to add)' }}
                </div>
                <textarea
                    placeholder="Bio"
                    v-if="editing_survey.description"
                    v-model="surveyStore.newSurvey.description"
                    @change="storeSurvey('description')"
                    class="textarea textarea-ghost textarea-bordered textarea-xs w-full max-w-xs focus:bg-gray-50"
                ></textarea>
            </div>
        </div>
        <!-- Button for adding questions -->
        <div
            :class="`bg-white rounded-xl align-self-start p-2 self-center ${questions.length > 0 ? 'invisible' : ''}`"
        >
            <!-- The plus sign -->
            <span class="tooltip" data-tip="Add a question" @click="addAQuestion(0)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </span>
        </div>
    </div>
        <div class="flex space-x-8 mb-2" v-for="(question, index) in computedQuestions" :key="question.id || index" draggable="true" @dragstart="startDrag(index)" @dragover="dragOver" @drop="drop">
            <div v-if="placeholderIndex === index && draggedItemIndex !== null" class="placeholder w-full" :style="{ top: placeholderIndex * itemHeight + 'px' }">
                <!-- Placeholder content (could be a line, an empty div, etc.) -->
                Placeholder
            </div>
            <RenderQuestion
                v-else
            class="flex-grow"
            :survey_id="$page.props.survey ? $page.props.survey.survey_id : null"
            @deleted="removeQuestion"
            @updating="focus_question"
            :focused="updating === index"
            :qtypes="$page.props.question_types"
            :data="question"
            :reference="index"
        ></RenderQuestion>
        <div :class="`bg-white p-2 flex flex-col mb-4 rounded-xl ${updating === index ? '' : 'invisible'}`">
            <!-- The plus sign -->
            <span class="tooltip" data-tip="Add a question above this" @click="addClicked(index, 'before')">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </span>
            <div class="flex-grow"></div>
            <span class="tooltip" data-tip="Add a question below this" @click="addClicked(index, 'after')">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </span>
        </div>

    </div>
</template>

<script>
import RenderQuestion from "@/Components/Custom/RenderQuestion.vue";
import { ref, onMounted, watch, computed } from "vue";
import { useSurveyStore } from "@/Stores/SurveyStore.js";
import axios from "axios";

export default {
    name: "SurveyAdminQuestions",
    props: ['survey', 'questions', 'inertia'],
    components: {
        RenderQuestion
    },
    setup(props, context) {
        const surveyStore = useSurveyStore();
        const editing_survey = ref({
            title: false,
            description: false
        });
        const survey_title_field = ref('survey_title_field');
        const updating = ref(0);
        const questions = ref([]);
        const new_question = ref({
            question_text: "",
            question_type: "",
            options: [],
            is_required: false
        });
        const newSurvey = ref({
            title: "",
            description: "",
            due_date: "",
            status: "draft",
            created_at: "",
            updated_at: ""
        });

        const draggedItemIndex = ref(null);
        const placeholderIndex = ref(null);
        const itemHeight = 40; // Adjust as needed

        const startDrag = (index, event) => {
            //event.preventDefault();
            draggedItemIndex.value = index;
        };

        const dragOver = (event) => {
            event.preventDefault();
            const rect = event.target.getBoundingClientRect();
            const mouseY = event.clientY - rect.top;
            placeholderIndex.value = Math.round(mouseY / itemHeight);
        };

        const computedQuestions = computed(() => {
            return surveyStore.questions;
        })

        const drop = async (event) => {
            console.log('you are dropping something')

            if (draggedItemIndex.value !== null) {
                const draggedItem = surveyStore.questions[draggedItemIndex.value];
                surveyStore.questions.splice(draggedItemIndex.value, 1);
                const dropIndex = placeholderIndex.value !== null ? placeholderIndex.value : surveyStore.questions.length;//Array.from(event.target.parentNode.children).indexOf(event.target);
                surveyStore.questions.splice(dropIndex, 0, draggedItem);
                console.log(`You are dragging item from ${draggedItemIndex.value} to ${dropIndex}`)
                console.log(surveyStore.questions);
                // Update the order in the database after drag-and-drop
                /*try {
                    const response = await axios.post('/api/updateQuestionOrder', {
                        questions: surveyStore.questions,
                    });
                    console.log('Order updated successfully:', response.data);
                } catch (error) {
                    console.error('Error updating question order:', error);
                }*/

                draggedItemIndex.value = null;
            }
        };

        // Watch for changes in the questions array and trigger a re-render
        watch(() => surveyStore.questions, () => {});

        const editSurveyTitle = () => {
            editing_survey.value.title = true;
            // focus on the element
            setTimeout(() => {
                document.getElementById('survey_title_field').focus();
            }, 20);

        }

        onMounted(() => {
            console.log(props.inertia);
            if (props.survey) {
                surveyStore.newSurvey = props.survey
                //newSurvey.value = props.survey;
                surveyStore.questions = props.questions;
            }
            if (surveyStore.questions.length > 0) {
                updating.value = surveyStore.questions.length - 1;
            }
            if(surveyStore.newSurvey.survey_id){
                fetchSurveyResponses()
            }
        });

        const addAQuestion = (position) => {
            //this.updating = (this.questions.length - 1)
            //alert('question is being added')
            const newQuestion = {
                question_text: '',
                question_type: 'short-answer',
                options: [],
                is_required: false,
            }

            if(position === 'before' || !surveyStore.questions.length){

                if(updating.value > 0){
                    surveyStore.questions.splice(updating.value , 0, newQuestion);
                    updating.value = updating.value - 1;
                }else{
                    surveyStore.questions.splice(updating.value , 0, newQuestion);
                }

                console.log('the if has run');
            }else{
                updating.value = updating.value + 1;
                surveyStore.questions.splice(updating.value, 0, newQuestion);

                console.log("the else has run here")
            }
        }

        const removeQuestion = (reference) => {
            surveyStore.questions.splice(reference, 1);
            if (reference > 0) {
                updating.value = reference - 1;
            }
           //emit("questionRemoved", reference);
        };

        const focus_question = (e) => {
            updating.value = e;
        };

        const fetchSurveyResponses = async () => {
            try {
                const response = await axios.get(route('surveys.responses', surveyStore.newSurvey.survey_id));
                surveyStore.responses = response.data; // Update the responses array with new data
                console.log('responses successfully pulled as shown below')
            } catch (error) {
                console.error()
                console.error('Error fetching survey responses:', error);
            }
        };

        const storeSurvey = async (variable = "all") => {
            if (variable !== "all") {
                editing_survey.value[variable] = false;
            }
            try {
                const response = await axios.post(route("surveys.store"), surveyStore.newSurvey);
                console.log(response)
                if (response.status === 200 || response.status === 201) {
                    console.log("Survey data has been stored:", response.data);
                    if (!props.survey) {
                        props.inertia.visit(route('survey.modify', response.data.stored.hashslug));
                        //Inertia.visit(route('survey.modify', response.data.stored.hashslug));
                    }
                } else {
                    console.log("Data could not be saved");
                }
            } catch (error) {
                console.error("Error while storing survey data:", error);
            }
        };

        const addClicked = (index, position) => {
            addAQuestion(position);
        }

        return {
            editing_survey,
            updating,
            questions,
            new_question,
            surveyStore,
            newSurvey,
            survey_title_field,
            draggedItemIndex,
            placeholderIndex,
            itemHeight,
            computedQuestions,
            startDrag,
            dragOver,
            drop,
            removeQuestion,
            focus_question,
            storeSurvey,
            addClicked,
            addAQuestion,
            fetchSurveyResponses,
            editSurveyTitle
        };
    }
};
</script>

<style scoped>
.placeholder {
    border: 2px dashed #333; /* Example styling for the placeholder */
    margin: 0;
    padding: 0;
    height: 40px; /* Adjust as needed */
}
</style>
