import { defineStore } from 'pinia'

export const useSurveyStore = defineStore('surveyStore', {
    state: () => ({
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
        settings: {
            'is_timed': false,
        },
        responses: [],
    }),
    actions: {
        classifiedResponses (question_index) {
            const index = question_index;
            const groupedResponses = {};
            let responses = this.responses.filter(el => el.question_id === this.questions[index].question_id);
            // Iterate through each response
            responses.forEach(response => {
                // Use response_text as the key for grouping
                const key = response.response_text;

                // If the key already exists, increment the count
                if (groupedResponses[key]) {
                    groupedResponses[key].count++;
                } else {
                    // If the key doesn't exist, create a new entry
                    groupedResponses[key] = {
                        response_text: key,
                        count: 1,
                        responses: [], // You can store the individual responses here if needed
                    };
                }

                // Store the individual response in the grouped entry
                groupedResponses[key].responses.push(response);
            });

            // Convert the object values to an array
            return Object.values(groupedResponses);
        },
        classifyCheckboxResponses(responses) {
            const classifiedCheckboxResponses = {};

            //console.log(responses)
            responses.forEach((response) => {
                // Assuming response.response_text is an array for checkbox-type questions
                const checkboxAnswers = JSON.parse(response.response_text);

                checkboxAnswers.forEach((answer) => {
                    if (!classifiedCheckboxResponses[answer]) {
                        classifiedCheckboxResponses[answer] = 0;
                    }

                    classifiedCheckboxResponses[answer] += 1;
                });
            });

            return classifiedCheckboxResponses;
        },
        classifyIndividualResponses(responses) {
            const classifiedResponses = {};

            responses.forEach(response => {
                const sessionId = response.session_id;

                // Check if the session_id is already a key in the classifiedResponses object
                if (!classifiedResponses[sessionId]) {
                    // If not, create a new array for that session_id
                    classifiedResponses[sessionId] = [];
                }

                // Add the response to the array for the corresponding session_id
                classifiedResponses[sessionId].push(response);
            });

            return classifiedResponses;
        },
        getSetting(key) {
            return this.settings[key]
        }
    },
    getters: {
        response_count: (state) => {
            return Object.keys(state.classifyIndividualResponses(state.responses)).length
        }
    },
})
