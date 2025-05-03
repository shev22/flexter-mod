<template>
<div>
    <Head title=" | Login"/>
    Login
</div>
    <div id="app">
        <form @submit.prevent="submit" class="registration-form">
            <TextInput name="email" type="email" v-model="form.email" :message="form.errors.email" />
            <TextInput name="password" v-model="form.password" :message="form.errors.name" />
            <div>
                <input type="checkbox" v-model="form.remember">
                <label>Remember me</label>
            </div>
            <button type="submit" :disabled="form.processing">Login</button>
        </form>
        <p class="sign-in-link">Need an account? <a href="login">Register</a></p>
    </div>
</template>

<script setup>
import {Head, useForm} from "@inertiajs/vue3";
import TextInput from "../../Components/TextInput.vue";

const form = useForm({
    email: null,
    password: null,
    remember: null
});

const submit =() => {
    form.post('login', {
        onError: () => form.reset('password, remember')
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

.sign-in-link a {
    color: #d0f1a3; /* Lemon green highlight */
    text-decoration: none;
}

.sign-in-link a:hover {
    text-decoration: underline;
}
</style>
