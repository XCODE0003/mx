<template>
    <div class="admin-referral-details">
        <div class="container">
            <div class="header">
                <h1>{{ link.name }}</h1>
                <a href="/admin/referrals" class="btn">← Назад к списку</a>
            </div>

            <div class="info-card">
                <h2>Информация о ссылке</h2>
                <p><strong>Код:</strong> <code>{{ link.code }}</code></p>
                <p><strong>URL:</strong> <a :href="link.url" target="_blank">{{ link.url }}</a></p>
                <p><strong>Описание:</strong> {{ link.description || 'Нет описания' }}</p>
                <p><strong>Статус:</strong> 
                    <span :class="link.is_active ? 'status-active' : 'status-inactive'">
                        {{ link.is_active ? 'Активна' : 'Неактивна' }}
                    </span>
                </p>
                <p><strong>Создана:</strong> {{ link.created_at }}</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ link.clicks }}</div>
                    <div class="stat-label">Кликов</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ registrations.length }}</div>
                    <div class="stat-label">Регистраций</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ purchases.length }}</div>
                    <div class="stat-label">Покупок</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ total_revenue }} ₽</div>
                    <div class="stat-label">Выручка</div>
                </div>
            </div>

            <div class="section">
                <h2>История переходов ({{ visits.length }})</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Пользователь</th>
                            <th>IP адрес</th>
                            <th>User Agent</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="visit in visits" :key="visit.id">
                            <td>{{ visit.visited_at }}</td>
                            <td>{{ visit.user ? `${visit.user.name} (${visit.user.email})` : 'Гость' }}</td>
                            <td>{{ visit.ip_address }}</td>
                            <td class="ua">{{ visit.user_agent }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="section">
                <h2>Регистрации ({{ registrations.length }})</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Имя</th>
                            <th>Email</th>
                            <th>Дата регистрации</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in registrations" :key="user.id">
                            <td>{{ user.id }}</td>
                            <td>{{ user.name }}</td>
                            <td>{{ user.email }}</td>
                            <td>{{ user.created_at }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="section">
                <h2>Покупки ({{ purchases.length }})</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID заказа</th>
                            <th>Пользователь</th>
                            <th>Сумма</th>
                            <th>Дата покупки</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="order in purchases" :key="order.id">
                            <td>{{ order.id }}</td>
                            <td>{{ order.user ? `${order.user.name} (${order.user.email})` : 'Гость' }}</td>
                            <td>{{ order.price }} ₽</td>
                            <td>{{ order.created_at }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    link: Object,
    visits: Array,
    registrations: Array,
    purchases: Array,
    total_revenue: Number,
});
</script>

<style scoped>
.admin-referral-details {
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

.info-card {
    background: #f5f5f5;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
}

.info-card h2 {
    font-size: 24px;
    margin-bottom: 15px;
}

.info-card p {
    margin: 10px 0;
    font-size: 16px;
}

code {
    background: white;
    padding: 4px 8px;
    border-radius: 3px;
    font-family: monospace;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}

.stat-value {
    font-size: 36px;
    font-weight: 700;
    color: #007bff;
}

.stat-label {
    font-size: 14px;
    color: #666;
    margin-top: 5px;
}

.section {
    margin-bottom: 40px;
}

.section h2 {
    font-size: 24px;
    margin-bottom: 15px;
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

.ua {
    font-size: 12px;
    max-width: 300px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.btn {
    padding: 8px 16px;
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    display: inline-block;
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
