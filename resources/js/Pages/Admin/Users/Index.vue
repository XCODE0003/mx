<template>
    <div class="admin-users">
        <div class="container">
            <div class="header">
                <h1>Пользователи ({{ users.length }})</h1>
                <a href="/admin/referrals" class="btn">Реферальные ссылки</a>
            </div>

            <div class="users-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Имя</th>
                            <th>Email</th>
                            <th>Реферальная ссылка</th>
                            <th>Дата регистрации</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id">
                            <td>{{ user.id }}</td>
                            <td>{{ user.name }}</td>
                            <td>{{ user.email }}</td>
                            <td>
                                <template v-if="user.referral">
                                    <code>{{ user.referral.code }}</code>
                                    <span class="ref-name">({{ user.referral.name }})</span>
                                </template>
                                <span v-else class="no-ref">—</span>
                            </td>
                            <td>{{ user.created_at }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    users: Array,
});
</script>

<style scoped>
.admin-users {
    padding: 40px 20px;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

h1 {
    font-size: 32px;
    margin: 0;
}

.users-table {
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

.ref-name {
    margin-left: 8px;
    color: #666;
    font-size: 14px;
}

.no-ref {
    color: #999;
}

.btn {
    padding: 8px 16px;
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    display: inline-block;
}
</style>
