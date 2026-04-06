<template>
    <div class="admin-referrals">
        <div class="container">
            <h1>Реферальные ссылки</h1>

            <div class="create-form">
                <h2>Создать новую ссылку</h2>
                <form @submit.prevent="createLink">
                    <div class="form-group">
                        <label>Название партнера *</label>
                        <input v-model="form.name" type="text" required />
                    </div>
                    <div class="form-group">
                        <label>Код (опционально)</label>
                        <input v-model="form.code" type="text" placeholder="Если не указать, будет сгенерирован автоматически" />
                    </div>
                    <div class="form-group">
                        <label>Описание</label>
                        <textarea v-model="form.description" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Создать ссылку</button>
                </form>
            </div>

            <div class="links-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Код</th>
                            <th>Ссылка</th>
                            <th>Клики</th>
                            <th>Регистрации</th>
                            <th>Покупки</th>
                            <th>Выручка</th>
                            <th>Активна</th>
                            <th>Создана</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="link in links" :key="link.id">
                            <td>{{ link.id }}</td>
                            <td>{{ link.name }}</td>
                            <td><code>{{ link.code }}</code></td>
                            <td>
                                <a :href="link.url" target="_blank">{{ link.url }}</a>
                                <button @click="copyToClipboard(link.url)" class="btn-copy">📋</button>
                            </td>
                            <td>{{ link.clicks }}</td>
                            <td>{{ link.registrations }}</td>
                            <td>{{ link.purchases }}</td>
                            <td>{{ link.revenue }} ₽</td>
                            <td>
                                <span :class="link.is_active ? 'status-active' : 'status-inactive'">
                                    {{ link.is_active ? 'Да' : 'Нет' }}
                                </span>
                            </td>
                            <td>{{ link.created_at }}</td>
                            <td>
                                <router-link :to="`/admin/referrals/${link.id}`" class="btn btn-sm">Подробнее</router-link>
                                <button @click="toggleActive(link)" class="btn btn-sm">
                                    {{ link.is_active ? 'Деактивировать' : 'Активировать' }}
                                </button>
                                <button @click="deleteLink(link)" class="btn btn-sm btn-danger">Удалить</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    links: Array,
});

const form = ref({
    name: '',
    code: '',
    description: '',
});

const createLink = () => {
    router.post('/admin/referrals', form.value, {
        onSuccess: () => {
            form.value = { name: '', code: '', description: '' };
        },
    });
};

const toggleActive = (link) => {
    router.put(`/admin/referrals/${link.id}`, {
        name: link.name,
        description: link.description,
        is_active: !link.is_active,
    });
};

const deleteLink = (link) => {
    if (confirm(`Удалить реферальную ссылку "${link.name}"?`)) {
        router.delete(`/admin/referrals/${link.id}`);
    }
};

const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text);
    alert('Ссылка скопирована в буфер обмена!');
};
</script>

<style scoped>
.admin-referrals {
    padding: 40px 20px;
}

h1 {
    font-size: 32px;
    margin-bottom: 30px;
}

.create-form {
    background: #f5f5f5;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 40px;
}

.create-form h2 {
    font-size: 24px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.links-table {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background: #f8f9fa;
    font-weight: 600;
}

code {
    background: #f5f5f5;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: monospace;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    margin-right: 5px;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-sm {
    padding: 4px 8px;
    font-size: 12px;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-copy {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 16px;
}

.status-active {
    color: green;
    font-weight: 600;
}

.status-inactive {
    color: red;
    font-weight: 600;
}
</style>
