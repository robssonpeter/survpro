<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

</script>
<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex">
                <h2 class="font-semibold self-center flex-grow text-xl text-gray-800 leading-tight">{{ newSurvey.title.length?newSurvey.title:'New Survey' }}</h2>
                <button onclick="document.getElementById('my_modal_1').showModal()" v-if="newSurvey.status === 'published'" class="btn btn-primary self-center">Share</button>
            </div>
            <section class="flex -mb-6" v-if="newSurvey.status !== 'draft'">
                <div class="flex-grow"></div>
                <div class="tabs">
                    <a :class="`tab hover:text-primary tab-bordered ${current_tab === 'questions'?activeTab:''}`" @click="currentTab('questions')">Questions</a>
                    <a :class="`tab hover:text-primary tab-bordered ${current_tab === 'responses'?activeTab:''}`" @click="currentTab('responses')">Responses</a>
                    <a :class="`tab hover:text-primary tab-bordered ${current_tab === 'settings'?activeTab:''}`" @click="currentTab('settings')">Settings</a>
                </div>
                <div class="flex-grow"></div>
            </section>
        </template>
        <dialog id="my_modal_1" class="modal show">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Share Survey</h3>
                <div>
                    <div class="form-control w-full max-w-xs mb-2 flex">
                        <label class="label">
                            <span class="label-text">Emails</span>
                        </label>
                        <input
                            v-model="newEmail"
                            @input="handleInput"
                            @keydown="handleKeyDown"
                            type="text"
                            placeholder="Type email and press Enter"
                            class="input input-bordered w-full max-w-xs flex-grow"
                        />
                    </div>

                    <div class="tags">
      <span
          v-for="(email, index) in share.emails"
          :key="index"
          class="tag badge badge-outline"
      >
        {{ email }}
        <button
            class="tag-remove"
            @click="removeEmail(index)"
        >
          <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              class="w-4 h-4"
          >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </button>
      </span>
                    </div>
                </div>

                <div class="form-control w-full max-w-xs mb-2">
                    <label class="label">
                        <span class="label-text">Subject</span>
                    </label>
                    <input type="text"  v-model="share.subject" placeholder="Type here" class="input input-bordered w-full max-w-xs" />
                </div>

                <div>
                    <label for="message">Message</label>
                    <QuillEditor theme="snow" content-type="html" v-model:content="share.message"/>
                </div>


                <div class="modal-action">
                    <form method="dialog">
                        <!-- if there is a button in form, it will close the modal -->
                        <button class="btn btn-neutral">Close</button>
                    </form>
                    <div class="mt">
                        <button class="btn btn-primary" @click="sendSurvey" :disabled="sendingSurvey"><span class="loading loading-bars loading-md" v-if="sendingSurvey"></span><span v-else>Send</span><!--Send--></button>
                    </div>
                </div>
            </div>
        </dialog>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-gray-600">
                <div class="md:px-40 animate__animated animate__fadeIn" >
                    <!-- Responses -->
                    <section v-if="current_tab==='responses'">
                        <SurveyAdminResponses :survey="newSurvey" />
                    </section>
                    <!-- End of Responses -->

                    <!-- Settings -->
                    <section v-if="current_tab==='settings'">
                        <SurveyAdminSettings />
                    </section>
                    <!-- End of Settings -->
                    <!-- New Survey Questions -->
                    <section v-if="current_tab==='questions'">
                        <SurveyAdminQuestions />
                    </section>
                    <!-- End New Survey Questions -->
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import RenderQuestion from "@/Components/Custom/RenderQuestion.vue";/*Remove*/
import SurveyAdminQuestions from "@/Components/Pages/Survey/SurveyAdminQuestions.vue";
import SurveyAdminResponses from "@/Components/Pages/Survey/SurveyAdminResponses.vue";
import SurveyAdminSettings from "@/Components/Pages/Survey/SurveyAdminSettings.vue";
import { useSurveyStore } from "@/Stores/SurveyStore.js";
import axios from "axios";
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import { ref, watch } from 'vue';
import swal from 'sweetalert';
export default {
    setup() {
        const editorContent = ref('');
        const surveyStore = useSurveyStore();
        // Watch for changes in the Quill editor content
        watch(editorContent, (newContent) => {
            // Assign the HTML content to the share.message variable
            // If you don't want HTML tags, you can use a sanitization library like DOMPurify
            this.share.message = newContent;
        });

        // Rest of your component setup

        return {
            editorContent, surveyStore
            // Other variables and methods
        };
    },
    components: [
        RenderQuestion, QuillEditor, SurveyAdminQuestions, SurveyAdminResponses, SurveyAdminSettings
    ],
    computed: {
        activeTab(){
            return 'tab-active text-primary border-b-primary font-semibold'
        }
    },
    data() {
        return {
            survey: useSurveyStore(),
            current_tab: 'questions',
            current_response_tab: 'summary',
            newEmail: '',
            sendingSurvey: false,
            share: {
                emails: [],
                subject: '',
                message: '[LINK]',
            },
            editing_survey: {
                title: false,
                description: false,
            },
            updating: 0,
            questions: [], // An array to store the list of surveys
            new_question: {
                question_text: '',
                question_type: '',
                options: [],
                is_required: false,
            },
            newSurvey: {
                title: '',
                description: '',
                due_date: '',
                status: 'draft',
                created_at: '',
                updated_at: '',
            },
        };
    },
    mounted() {

        this.survey.name = "hello there";
        // Fetch surveys from your API and populate the 'surveys' array
        if(this.$page.props.survey){
            // set the new survey details
            this.newSurvey = this.$page.props.survey;
            this.questions = this.$page.props.questions;
        }
        if (this.questions.length > 0){
            this.updating = this.questions.length - 1
        }
        // You can use Inertia's inertia.get() here
    },
    methods: {
        currentTab(tab, type='current_tab'){
            this[type] = tab;
        },
        handleInput() {
            // Handle input in the text field
        },
        handleKeyDown(event) {
            if (event.key === 'Enter' || event.key === ',') {
                event.preventDefault();
                this.addEmail();
            }
        },
        updateEditorContent(content){
            console.log(content)
            //this.share.message = content
        },
        addEmail() {
            // Add the new email to the 'share.emails' array
            if (this.newEmail.trim() !== '' && this.validateEmail(this.newEmail.trim())) {
                this.share.emails.push(this.newEmail.trim());
                this.newEmail = '';
            }
        },
        validateEmail(email) {
            // Regular expression for email validation
            const emailRegex = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i;
            return emailRegex.test(email);
        },
        removeEmail(index) {
            // Remove an email from the 'share.emails' array
            this.share.emails.splice(index, 1);
        },
        removeQuestion(reference) {
            // Find the index of the question with the given reference
            const index = reference;

            // Splice the question from the questions array
            this.questions.splice(index, 1);

            // Update the updating variable if the reference is greater than 0
            if (reference > 0) {
                this.updating = reference - 1;
            }

            // Emit an event to notify child components about the question removal
            this.$emit('questionRemoved', reference);
        },
        focus_question(e){
            this.updating = e;
            //console.log(document.getElementById(e))

        },
        addClicked(index, position){
            //console.log(index);
            console.log(position);
            this.addAQuestion(position);
        },
        addAQuestion(position){
            //this.updating = (this.questions.length - 1)
            const newQuestion = {
                question_text: '',
                question_type: 'short-answer',
                options: [],
                is_required: false,
            }

            if(position === 'before' || !this.questions.length){

                if(this.updating > 0){
                    this.questions.splice(this.updating , 0, newQuestion);
                    this.updating = this.updating - 1;
                }else{
                    this.questions.splice(this.updating , 0, newQuestion);
                }

                console.log('the if has run');
            }else{
                this.updating = this.updating + 1;
                this.questions.splice(this.updating, 0, newQuestion);

                console.log("the else has run here")
            }
        },
        async storeSurvey(variable = 'all') {
            if (variable !== 'all') {
                this.editing_survey[variable] = false;
            }
            console.log(this.newSurvey)
            try {
                const response = await axios.post(route('surveys.store'), this.newSurvey);
                // Assuming your Laravel route for storing surveys is '/api/surveys'
                if (response.status === 200 || response.status === 201) {
                    // Survey data has been successfully stored in the database
                    // You can handle the response as needed
                    console.log('Survey data has been stored:', response.data);
                    if(!this.$page.props.survey){
                        this.$inertia.visit(route('survey.modify', response.data.stored.hashslug));
                    }
                } else {
                    // Handle errors or display a message to the user
                    console.log('data could not be saved')
                }
            } catch (error) {
                // Handle any errors that occur during the request
                console.error('Error while storing survey data:', error);
            }
        },
        async sendSurvey() {
            // Prepare the data to send, including the share object
            const requestData = {
                // Include other data you need to send, e.g., subject
                emails: this.share.emails,
                subject: this.share.subject,
                message: this.share.message,
                survey_hash: this.newSurvey.hashslug,
            };

            try {
                this.sendingSurvey = true;
                // Send a POST request to your server to handle the email sending
                const response = await axios.post(route('survey.send'), requestData);

                if (response.status === 200) {
                    // Successfully sent the survey
                    console.log('Survey sent:', response.data);

                    // You can reset the share object or perform other actions as needed
                    this.share.emails = [];
                    this.share.subject = '';
                    this.share.message = '';

                    // Close the modal or perform other UI actions
                    document.getElementById('my_modal_1').close();
                    swal("Sent!", "Survey successfully sent!", "success");
                    this.sendingSurvey = false;
                } else {
                    // Handle errors or display a message to the user
                    console.error('Error sending survey:', response.data);
                    this.sendingSurvey = false;
                }
            } catch (error) {
                // Handle any errors that occur during the request
                console.error('Error sending survey:', error);
                this.sendingSurvey = false;
            }
        },
        editSurvey(survey) {
            // Implement the edit functionality to update a survey
            // You can open a modal or redirect to an edit page
        },
        deleteSurvey(survey) {
            // Implement the delete functionality to delete a survey
            // You can confirm the delete action and then remove the survey from the list
        },
    },
    created() {
        // Create a debounced version of the store_question method
        //alert('things are happening')
        //this.debouncedStoreSurvey = debounce(this.storeSurvey(), 1000); // Adjust the delay as needed (e.g., 1000ms)
    }
};
</script>

<style scoped>
.slide-fade-enter-active, .slide-fade-leave-active {
    transition: opacity 0.5s, transform 0.5s;
}
.slide-fade-enter, .slide-fade-leave-to {
    opacity: 0;
    transform: translateY(10px);
}
</style>


