
<template>

    <div>
        <Head title=" | Register"/>
        Register
    </div>

    <div id="app">
        <form @submit.prevent="submit" class="registration-form">

            <TextInput name="name" v-model="form.name" :message="form.errors.name" />
            <TextInput name="email" type="email" v-model="form.email" :message="form.errors.email" />
            <TextInput name="password" v-model="form.password" :message="form.errors.name" />
            <TextInput name="confirm password" v-model="form.password_confirmation" />

            <button type="submit" :disabled="form.processing">Register</button>
        </form>

        <p class="sign-in-link">Already registered? <a href="login">Sign in</a></p>
    </div>
</template>

<script setup>

import {Head, useForm} from "@inertiajs/vue3";
import TextInput from "../../Components/TextInput.vue";


        const form = useForm({
            name: null,
            email: null,
            password: null,
            password_confirmation: null
        });

       const submit =() => {
            form.post('register', {
                onError: () => form.reset('password, password_confirmation')
            })
        }


</script>

<style scoped>

.registration-form {
    background-color: #fff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
button {
    background-color: #d0f1a3; /* Lemon green highlight */
    color: #333;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

button:hover {
    background-color: #c2e692; /* Darker lemon green on hover */
}

.sign-in-link {
    margin-top: 15px;
    text-align: center;
}

.sign-in-link a {
    color: #d0f1a3; /* Lemon green highlight */
    text-decoration: none;
}

.sign-in-link a:hover {
    text-decoration: underline;
}
</style>
