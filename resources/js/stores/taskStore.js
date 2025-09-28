import { defineStore } from "pinia";
import axiosClient from "../api/axios";
export const useTaskStore = defineStore("task", {
    state: () => ({
        loading: false,
        errors: null,
        selectedGrade: 'oge',
        selectedSubject: null,
        totalTasks: 0,
        subjects: [],
        grades: [
            { key: 'oge', value: 'ОГЭ' },
            { key: 'ege', value: 'ЕГЭ' }
        ],
        groups: [],
        // Текущее выбранное группа для просмотра задач
        selectedGroup: null,
        // Задачи текущей группы
        tasks: [],
        // Выбранное задание по каждой группе: { [groupId]: taskId }
        selectedTaskByGroup: {}
    }),

    getters: {
        isLoading: (state) => state.loading,
        // Проверка выбранности задания в своей группе
        isTaskSelected: (state) => (task) => {
            const groupId = (state.selectedGroup && state.selectedGroup.id)
                || task?.mark || task?.group?.id || task?.group_id || null;
            if (!groupId) return false;
            return state.selectedTaskByGroup[groupId] === task.id;
        },
        // Есть ли выбранное задание у конкретной группы
        groupHasSelection: (state) => (groupId) => {
            return Boolean(state.selectedTaskByGroup[groupId]);
        },
    },

    actions: {

        clearErrors() {
            this.errors = null;
        },

        async getSubjects() {
            const response = await axiosClient.get(`/profile/subjects/${this.selectedGrade}`);
            this.subjects = response.data.map((subject) => ({
                key: subject.id,
                value: subject.name,
            }));
            this.selectedSubject = this.subjects[0].key;
        },

        async exportPdfManual(tasks) {
            const response = await axiosClient.post(`/manual/tasks/download`, {
                tasks: tasks
            });
            return response.data;
        },
        async exportPdfAuto(subjectId) {
            const response = await axiosClient.post(`/auto/tasks/download`, {
                task_subject_id: subjectId
            });
            return response.data;
        },
        async getAutoStatus(taskId, file) {
            const response = await axiosClient.get(`/auto/tasks/status/${taskId}/${encodeURIComponent(file)}`);
            return response.data;
        },
        async getManualStatus(taskId, file) {
            const response = await axiosClient.get(`/manual/tasks/status/${taskId}/${encodeURIComponent(file)}`);
            return response.data;
        },

        async getGroups() {
            const response = await axiosClient.get(`/profile/groups/${this.selectedGrade}/${this.selectedSubject}`);
            this.groups = response.data.groups;
            this.totalTasks = response.data.total;
        },

        setSelectedGroup(group) {
            this.selectedGroup = group || null;
        },

        async getTasks(selectedGroup) {
            const group = selectedGroup || this.selectedGroup;
            if (!group || !group.id) {
                this.tasks = [];
                return { tasks: [] };
            }
            const response = await axiosClient.get(`/profile/tasks/${group.id}`);
            this.tasks = response.data.tasks || [];
            return response.data;
        },

        // Переключить выбор задания в пределах своей группы (ровно одно на группу)
        toggleTaskSelection(task) {
            const groupId = (this.selectedGroup && this.selectedGroup.id)
                || task?.mark || task?.group?.id || task?.group_id || null;
            if (!groupId || !task || !task.id) return;
            if (this.selectedTaskByGroup[groupId] === task.id) {
                const next = { ...this.selectedTaskByGroup };
                delete next[groupId];
                this.selectedTaskByGroup = next;
            } else {
                this.selectedTaskByGroup = { ...this.selectedTaskByGroup, [groupId]: task.id };
            }
        },

    }

});
