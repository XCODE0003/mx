import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

export function useChat() {
    const chats = ref([]);
    const activeChat = ref(null);
    const messages = ref([]);
    const loading = ref(false);
    const sending = ref(false);

    function initializeEcho() {
        console.log('initializeEcho для чата:', activeChat.value.id);

        window.Echo.private(`chat.${activeChat.value.id}`)
            .listen('MessageSent', (e) => {
                console.log("Получено событие MessageSent:", e);
                if (!messages.value.some(message => message.id === e.message.id)) {
                    messages.value.push(e.message);
                }
            })
            .error((error) => {
                console.error('Ошибка Echo:', error);
            });

        console.log('Echo подписка создана');
    }

    const getUserChats = async () => {
        loading.value = true;
        try {
            const response = await axios.get('/chat/');
            chats.value = response.data;
        } catch (error) {
            console.error('Ошибка загрузки чатов:', error);
        } finally {
            loading.value = false;
        }
    };

    const selectChat = async (chat) => {
        activeChat.value = chat;
        await loadChatMessages(chat.id);
    };

    const loadChatMessages = async (chatId) => {
        loading.value = true;
        try {
            const response = await axios.get(`/chat/${chatId}/messages`);
            messages.value = response.data.map(msg => ({
                ...msg,
                message: msg.message || ''
            }));
            initializeEcho();
        } catch (error) {
            console.error('Ошибка загрузки сообщений:', error);
        } finally {
            loading.value = false;
        }
    };

    const sendMessage = async (message, chatId = null) => {
        if (!message.trim() || sending.value) return;

        const targetChatId = chatId || activeChat.value?.id;
        if (!targetChatId) return;

        sending.value = true;
        try {
            const response = await axios.post('/chat/send', {
                chat_id: targetChatId,
                message: message.trim()
            });
            messages.value.push(response.data.message);
            return response.data;
        } catch (error) {
            console.error('Ошибка отправки сообщения:', error);
            throw error;
        } finally {
            sending.value = false;
        }
    };
    const getChats = async () => {
        loading.value = true;
        try {
            const response = await axios.get('/chat/chats');
            return response.data;
        } catch (error) {
            console.error('Ошибка загрузки чатов:', error);
        } finally {
            loading.value = false;
        }
    }


    const markAsRead = async (chatId) => {
        try {
            await axios.post(`/chat/${chatId}/read`);
        } catch (error) {
            console.error('Ошибка отметки как прочитанное:', error);
        }
    };



    return {
        chats,
        activeChat,
        messages,
        loading,
        sending,
        getUserChats,
        selectChat,
        sendMessage,
        markAsRead,
        getChats
    };
}
