<template>
    <div class="container sm:w-full md:w-full lg:w-1/2 mx-auto my-10">
        <h4 class="mb-2">City Weather Search</h4>
        <input class="bg-white focus:outline-none focus:shadow-outline border border-gray-300 rounded-lg py-2 mb-1 px-4 block w-full appearance-none leading-normal"
               type="text" placeholder="Search city (Auckland etc)" v-model="search_term" v-on:keyup="delay" v-on:keyup.enter="search">
        <div v-bind:class="[{'py-1 h-64 overflow-auto': cities.length}]" class=" bg-gray-100 rounded-lg fixed" style="width:inherit">
            <div v-if="searching_city_flg" class="p-2"><i class="fas fa-circle-notch fa-spin"></i> Loading cites...</div>
            <a href="#" v-for="city in cities" v-bind:key="city.id" class="block px-4 py-2 text-gray-800 hover:bg-indigo-500 hover:text-white"
               @click="getWeather(city.detail_link, city.full_name)">
                {{ city.full_name}}
            </a>
        </div>
        <div v-if="!cities" class="bg-blue-100 p-2 alert-warning text-red-400 my-2">No cities found</div>
        <div class="mt-2 py-2 w-full bg-white rounded-lg" >
            <div v-if="searching_weather_flg"><i class="fas fa-circle-notch fa-spin"></i> Loading weekly weather...</div>
            <h5 v-if="weathers.length">Weekly weather for {{city_name}}</h5>
            <table class="table-auto border-collapse w-full my-2">
                <tbody>
                    <tr>
                        <td class="py-5 border text-center" v-for="weather in weathers" v-bind:key="weather.date">
                            <span>{{ weather.date }}</span>
                            <span class="weather-icon"><img v-bind:src="weather.icon"/></span>
                            <span>{{ weather.temperature }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>

    import axios from 'axios';
    export default {
        mounted() {
            console.log('Component mounted.')
        },
        data() {
            return {
                weathers: [],
                weather: {
                    date: '',
                    summary: '',
                    icon: '',
                    temperature: '',
                    low_temperature: '',
                    high_temperature: ''
                },
                cities: [],
                city: {
                    full_name: '',
                    detail_link: ''
                },
                city_name: '',
                search_term: '',
                searching_city_flg: false,
                searching_weather_flg: false,
                timer: null,
            }
        },
        methods: {

            delay() {
                if (this.timer) {
                    clearTimeout(this.timer);
                }

                this.timer = setTimeout( () => this.search(), 1000);
            },

            search() {
                if (this.timer) {
                    clearTimeout(this.timer);
                }

                this.cities = []

                if (!this.search_term) {
                    return
                }

                if(this.searching_city_flg) {
                    return
                } else {
                    this.searching_city_flg = true;
                }

                axios.post('search', {
                    term: this.search_term
                }).
                then(res => {
                    if (res.data.success) {
                        this.cities = res.data.data.cities
                    } else {
                        this.cities = false
                    }
                    console.log(res)
                })
                .catch(e => {
                    this.errors.push(e)
                }).finally(()=> {
                    this.searching_city_flg = false;
                })
            },
            getWeather (link, name) {
                this.cities = [];
                this.search_term = '';
                this.searching_weather_flg = true
                this.weathers = []
                this.city_name = name

                axios.post('get-city-weather', {
                    link: link
                }).
                then(res => {
                    if (res.data.success) {
                        this.weathers = res.data.data.weathers
                    } else {
                        this.weathers = []
                    }
                    console.log(res.data.data.weathers)
                })
                .catch(e => {
                    this.errors.push(e)
                }).finally(()=> {
                    this.searching_weather_flg = false;
                })
            }
        }
    }
</script>
