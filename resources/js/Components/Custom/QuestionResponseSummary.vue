<template>
    <div class="card bg-white shadow-md flex-grow p-4 mb-4">
        <span class="font-bold">{{ surveyStore.questions[index].question_text }}</span>
<!--        <span>{{ classifiedResponses() }}</span>-->
        <small>{{ responses.length }} Responses</small>
        <section style="max-height: 500px; overflow-y: auto" v-if="surveyStore.questions[index].question_type === 'checkbox'">
            <canvas ref="checkboxChart" :id="`checkbox-chart-${index}`"></canvas>
        </section>
        <section style="max-height: 500px; overflow-y: auto" v-if="isGroupable">
            <div v-for="response in classifiedResponses()" class="bg-gray-200 p-2 rounded-md mb-2">
                <span>{{ response.response_text }}</span>
            </div>
        </section>
        <section style="max-height: 500px; overflow-y: auto" v-if="isGraphable">
            <div>
                <canvas ref="myChart" :id="`chart-${index}`"></canvas>
            </div>
        </section>
        <section style="max-height: 500px; overflow-y: auto" v-if="surveyStore.questions[index].question_type === 'date'">
            <div class="overflow-x-auto">
                <table class="table">
                    <!-- head -->
                    <thead>
                    <tr>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- row 2 -->
                    <tr class="" v-for="key in Object.keys(classifiedDateResponses)">
                        <th>{{ key }}</th>
                        <td>
                            <section class="flex space-x-1">
                                <button class="bg-gray-300 flex space-x-1 rounded-xl p-1" v-for="resp_key in Object.keys(classifiedDateResponses[key])">
                                    <span>{{ resp_key }}</span>
                                    <div class="bg-primary rounded rounded-xl text-sm px-1 text-white" v-if="classifiedDateResponses[key][resp_key].count > 1">
                                        {{ classifiedDateResponses[key][resp_key].count }}
                                    </div>
                                </button>
                            </section>
                        </td>
                    </tr>
                    <!-- row 3 -->
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useSurveyStore } from "@/Stores/SurveyStore.js";
import { Chart, CategoryScale, LinearScale, BarController } from "chart.js/auto";

export default {
    name: "QuestionResponseSummary",
    props: ['index'],

    setup(props){
        const surveyStore = useSurveyStore()

        const responses = computed(() => {
            return surveyStore.responses.filter(el => el.question_id === surveyStore.questions[props.index].question_id)
        })

        const question = computed(() => {
            return surveyStore.questions[props.index];
        });

        const isGroupable = computed(() => {
            let allowed = ['paragraph', 'short-answer']
            return allowed.indexOf(surveyStore.questions[props.index].question_type) >= 0
        })

        const isGraphable = computed(() => {
            let allowed = ['number', 'multiple-choice', 'time']
            return allowed.indexOf(surveyStore.questions[props.index].question_type) >= 0
        })

        const classifiedResponses = () => {
                const groupedResponses = {};
                let responses = surveyStore.responses.filter(el => el.question_id === surveyStore.questions[props.index].question_id);
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
        };

        const classifyResponsesForChart = (responses) => {
            // Create an object to store the grouped responses
            const groupedResponses = {};

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
                    };
                }
            });

            // Convert the object values to an array
            return Object.values(groupedResponses);
        }

        const fetchSurveyResponses = async () => {
            try {
                const response = await axios.get(route('surveys.responses', surveyStore.newSurvey.survey_id));
                responses.value = response.data; // Update the responses array with new data
            } catch (error) {
                console.error('Error fetching survey responses:', error);
            }
        };

        const classifyResponsesByDate = (responses) => {
            const classifiedResponses = {};

            responses.forEach((response) => {
                // Assuming response.created_at is a valid date string
                const responseDate = new Date(response.response_text);
                const formattedMonth = `${new Intl.DateTimeFormat('en-US', {
                    month: 'long',
                }).format(responseDate)} ${responseDate.getFullYear()}`;
                const formattedDay = responseDate.getDate();

                if (!classifiedResponses[formattedMonth]) {
                    classifiedResponses[formattedMonth] = {};
                }

                const monthKey = formattedMonth;
                const dayKey = formattedDay;

                if (!classifiedResponses[monthKey][dayKey]) {
                    classifiedResponses[monthKey][dayKey] = {
                        date: `${formattedMonth} ${formattedDay}`,
                        count: 0,
                        responses: [],
                    };
                }

                classifiedResponses[monthKey][dayKey].count += 1;
                classifiedResponses[monthKey][dayKey].responses.push(response);
            });

            return classifiedResponses;
        }



        onMounted(() => {
            const myChartRef = ref(null);
            const checkboxCharRef = ref(null);
            myChartRef.value = document.getElementById(`chart-${props.index}`)
            checkboxCharRef.value = document.getElementById(`checkbox-chart-${props.index}`)
            /*setTimeout(()=> {
                console.log)
            }, 100)*/

            if (myChartRef.value){
                const ctx = document.getElementById(`chart-${props.index}`).getContext('2d');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: classifiedResponsesForChart.value.map(response => response.response_text),
                        datasets: [{
                            label: 'Response Count',
                            data: classifiedResponsesForChart.value.map(response => response.count),
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
            // for checkbox chart
            if (checkboxCharRef.value){
                const ctxb = document.getElementById(`checkbox-chart-${props.index}`).getContext('2d');
                const data = {
                    labels: Object.keys(surveyStore.classifyCheckboxResponses(responses.value)),
                    datasets: [
                        {
                            label: 'Response Count',
                            data: Object.values(surveyStore.classifyCheckboxResponses(responses.value)),
                            backgroundColor: 'rgba(75, 192, 192, 0.2)', // Adjust the color as needed
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1,
                        },
                    ],
                };

                const options = {
                    indexAxis: 'y',
                    xLabels: 'Count',
                    scales: {
                        x: {
                            beginAtZero: true,
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Responses',
                            },
                        },
                    },
                };
                new Chart(ctxb, {
                    type: 'bar',
                    data,
                    options,
                });

            }
            if (surveyStore.questions[props.index].question_type === 'date'){
                console.log(classifyResponsesByDate(responses.value))
            }
        });

        const classifiedResponsesForChart = computed(() => classifyResponsesForChart(responses.value));
        const classifiedDateResponses = computed(() => classifyResponsesByDate(responses.value));

        return {
            surveyStore,
            responses,
            question,
            isGroupable,
            isGraphable,
            classifiedResponsesForChart,
            classifiedDateResponses,
            fetchSurveyResponses,
            classifiedResponses,
            classifyResponsesByDate,
            classifyResponsesForChart,
        }
    },
    mounted() {
        //alert('things are happening')
    }
}
</script>

<style scoped>

</style>
