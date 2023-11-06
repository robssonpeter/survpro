<template>
    <div :class="focusingStyling" @click="$emit('updating', reference)">
        <div class="flex space-x-6" v-if="focused">
            <!-- Input field for the question text -->
            <input
                v-model="question.question_text"
                class="p-2 mb-2 border rounded flex-grow"
                type="text"
                placeholder="Enter your question" />
            <!-- Dropdown for selecting the question type -->
            <select v-model="question.question_type" class="w-50 p-2 mb-2 border rounded flex-grow-0">
                <option :value="qtype['type']" v-for="qtype in qtypes">{{ qtype['label'] }}</option>
                <!-- Add more question types as needed -->
            </select>
        </div>
        <span class="text-neutral cursor-pointer mb-2" v-else>{{ reference+1 }}. {{ question.question_text?question.question_text:'Click to write a question..' }}</span>

        <div v-if="question.question_type === 'short-answer'" class="">
            <span class="underline py-2">Short-answer text</span>
        </div>
        <!-- Render additional fields based on the question type -->
        <div v-if="question.question_type === 'multiple-choice' || question.question_type === 'drop-down' || question.question_type === 'checkbox'" class="mb-2">
            <div v-for="(option, index) in question.options" :key="index" class="flex items-center py-1">
                <input v-if="question.question_type === 'multiple-choice'"
                       type="checkbox" name=""
                       id=""
                       class="rounded-full mr-2 cursor-pointer"
                       disabled>
                <input
                    v-if="question.question_type === 'checkbox'"
                    type="checkbox" class="mr-2" id="" disabled>
                <span v-if="question.question_type === 'drop-down'" class="mr-4">{{ index + 1 }}. </span>
                <div>
                    <input
                        v-if="focused"
                        v-model="option.text"
                        :ref="optionRef(index)"
                        :id="optionRef(index)"
                        type="text"
                        :placeholder="`Option ${index+1}`"
                        class="w-75 p-2 text-gray-700 border-b border-green-500 border-top-0 rounded" />
                    <span class="text-gray-700" v-else>{{ option.text?option.text:'Undefined' }}</span>
                </div>
                <span v-if="focused" @click="removeOption(index)" class="ml-2 text-red-500 hover:text-red-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </span>
                <div class="flex-grow"></div>
            </div>

            <div class="flex mt-2" v-if="focused">
                <input type="checkbox" name="" id="" class="rounded-full mr-2 cursor-pointer self-center" disabled>
                <input @focus="addOption" type="text" placeholder="Add Option" class="p-2" />
                <div class="flex-grow"></div>
            </div>
<!--            <button @click="addOption" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600">
                Add Option
            </button>-->
        </div>
        <div class="flex space-x-4" v-if="focused">
            <div class="flex-grow"></div>
            <!-- Switch for marking the question as required -->
            <div class="items-center pt-2">
                <div class="tooltip" data-tip="Duplicate Question">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 self-center">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                </svg>
                </div>
            </div>
            <label class="flex items-center">
                <input type="checkbox" v-model="question.required" class="mr-2" />
                Required
            </label>

            <!-- Delete button -->
            <div class="tooltip" data-tip="Delete">
            <button @click="deleteQuestion" class="text-red-500 p-2 rounded">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
            </button>
            </div>
        </div>
    </div>
</template>

<script>
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import FloatingActionBar from "@/Components/Custom/FloatingActionBar.vue";
import debounce from "lodash/debounce";
export default {
    components: [
        Dropdown, DropdownLink, FloatingActionBar
    ],
    props: {
      qtypes: {
          type: Object,
          required: true
      },
      reference: { // unique identifier for the component
          required: true
      },
      focused: {
          type: Boolean,
      },
      survey_id: {
          type: Number,
      },
      data: {
          type: Object
      }
    },
    computed: {
        options(){

        },
        focusingStyling(){
            if(this.focused){
                return 'p-4 card cursor-pointer border-l border-l-primary-focus/100 bg-white border-gray-200 shadow-md mb-4'
            }else{
                return 'p-4 card cursor-pointer border-l bg-white border-gray-200 shadow-md  mb-4'
            }
        },
        optionRef(){
          return index => {
              if(index === this.question.options.length - 1){
                  return `last_option_${this.reference}`;
              }else{
                  return `opt_${this.reference}_${index}`;
              }
          }
      }
    },
    watch: {
        // Watch for changes in question properties
        question: {
            deep: true, // Watch nested properties
            handler(newQuestion, oldQuestion) {
                // Call the store_question method when any question property changes
                //this.store_question(newQuestion);
                this.debouncedStoreQuestion(newQuestion);
            },
        },
    },
    mounted(){
        let data = this.data;
        if(typeof(data.options) == 'string'){
            data.options = JSON.parse(data.options)
        }
        this.question = data;
      // process the options
    },
    data() {
        return {
            question: {
                question_id: '',
                question_text: '',
                question_type: 'short-answer',
                survey_id: this.survey_id,
                required: false,
                options: [], // For multiple-choice and dropdown questions
                index_number: this.reference,
            },
            debouncedStoreQuestion: null,
        };
    },
    methods: {
        focusQuestion(){
          this.$emit('updating', )
        },
        deleteQuestion() {
            if (this.question.question_id) {
                // If question has question_id, make an API request to delete it in the database
                // Replace this with your actual API request
                axios
                    .delete(`/api/questions/${this.question.question_id}`)
                    .then(() => {
                        // Emit a deleted event to notify the parent component
                        this.$emit('deleted', this.reference);
                    })
                    .catch((error) => {
                        // Handle the error here
                        console.error('Error deleting question:', error);
                    });
            } else {
                // If question doesn't have a question_id, emit a deleted event directly
                this.$emit('deleted', this.reference);
            }
        },
        addOption() {
            // Add a new option for multiple-choice or dropdown question
            this.question.options.push({ text: '' });
            // click the last element
            let last_index = this.question.options.length - 1
            let ref = `last_option_${this.reference}`

            setTimeout(() => {
                document.getElementById(ref).focus();
            }, 50);
        },
        removeOption(index) {
            // Remove an option from multiple-choice or dropdown question
            this.question.options.splice(index, 1);
        },
        store_question(updatedQuestion) {
            if(!updatedQuestion.question_text){
                return false;
            }
            // Implement your logic to store the updated question data.
            // You can send an API request to save the changes to your backend.
            console.log("Storing question:", updatedQuestion);
            // Example API request using axios (make sure to import axios)
            axios.post(route('questions.store'), {
              question_id: updatedQuestion.question_id,
              question_text: updatedQuestion.question_text,
              question_type: updatedQuestion.question_type,
              options: updatedQuestion.options,
              required: updatedQuestion.required,
              index_number: updatedQuestion.index_number,
              survey_id: this.survey_id,
              // Add other fields as needed
            })
            .then((response) => {
              console.log("Question updated:", response.data);
              // update the data of the question
                this.question.question_id = response.data.question.question_id
                //this.question = response.data.question;
            })
            .catch((error) => {
              console.error("Error updating question:", error);
            });
        },
    },
    created() {
        // Create a debounced version of the store_question method
        //alert('things are happening')
        this.debouncedStoreQuestion = debounce(this.store_question, 1000); // Adjust the delay as needed (e.g., 1000ms)
    }
};
</script>
