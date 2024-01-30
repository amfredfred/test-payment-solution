<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { User } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    amount: 0,
    recipient_id:null
})



defineProps<{
    balance: number | string;
    users: User[]
}>();


const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('amount');
        },
    });
};


const onSelect = (selected: any) => {
    console.log(selected, 'SELECTED')
}


</script>

<style>
.blance-container {
    padding-inline: 25px;
    padding-bottom: 20px;
    border-radius: 20px;
    width: 320px;
}

.form-container {
    width: 100%;
    padding: 10px;
    box-shadow: 0 0 1px inset black;
    background: white;
    border-radius: 5px;
    margin-top: 30px;
}
</style>
 

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">Transfer To User</div>
                    <div class="blance-container col-3">
                        Balance ${{ balance }}
                    </div>
                </div>

                <div class="form-container">
                    <form :style="'display:flex; flex-wrap:wrap; gap:10px'">
                        <select @change="({ target: { value } }) => onSelect(value)" name="recipient_id" id="">
                            <option v-for=" user  in  users " :value="user.id">{{ user.name }}</option>
                        </select>
                        <input :placeholder="'$0.00'" :type="'number'" :name="'amount'" id="">
                        <button :class="''"
                            :style="'padding: 10px 40px; background: green; border-radius: 50px;color:white; margin-left:auto'">Send</button>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
