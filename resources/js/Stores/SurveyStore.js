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
    }),
})
