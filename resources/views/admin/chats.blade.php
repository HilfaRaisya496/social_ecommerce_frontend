@extends('layouts.admin')

@section('header_title', 'Chat Admin')

@section('content')
    <div x-data="chatSystem()" x-init="init()"
        class="bg-white rounded-xl shadow-sm border border-gray-100 h-[calc(100vh-12rem)] flex overflow-hidden">
        <!-- Sidebar Chats -->
        <div class="w-full md:w-80 border-r border-gray-200 flex-col shrink-0 transition-all duration-300"
            :class="selectedContact ? 'hidden md:flex' : 'flex'">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900 mb-3">Daftar Chat</h2>
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                    <input type="text" x-model.debounce.300ms="searchQuery" placeholder="Cari percakapan..."
                        class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent" />
                </div>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar">
                <template x-for="contact in filteredContacts" :key="contact.id">
                    <div @click="selectContact(contact)"
                        :class="{'bg-amber-50': selectedContact && selectedContact.id == contact.id}"
                        class="p-4 border-b border-gray-50 cursor-pointer hover:bg-gray-50 transition-all">
                        <div class="flex gap-3">
                            <div :class="contact.role === 'seller' ? 'bg-emerald-100' : 'bg-emerald-50'"
                                class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 overflow-hidden">
                                <template x-if="contact.avatar">
                                    <img :src="contact.avatar" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!contact.avatar">
                                    <span :class="contact.role === 'seller' ? 'text-emerald-700' : 'text-emerald-600'"
                                        class="font-bold" x-text="contact.name.charAt(0)"></span>
                                </template>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start mb-1">
                                    <h4 class="text-sm font-bold text-gray-900 truncate" x-text="contact.name"></h4>
                                    <span class="text-[10px] text-gray-400" x-text="contact.last_time"></span>
                                </div>
                                <div class="flex items-center gap-1.5 flex-1 min-w-0">
                                    <p :class="contact.unread_count > 0 ? 'font-bold text-gray-900' : 'text-gray-500'"
                                        class="text-xs truncate flex-1" x-text="contact.last_message || 'Belum ada pesan'">
                                    </p>
                                    <template x-if="contact.unread_count > 0">
                                        <span class="w-2 h-2 bg-amber-500 rounded-full shrink-0"></span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="filteredContacts.length === 0" class="p-8 text-center text-gray-400">
                    <i data-lucide="message-square" class="w-8 h-8 mx-auto mb-2 opacity-20"></i>
                    <p class="text-xs italic">Tidak ada percakapan aktif.</p>
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 flex-col bg-gray-50/20 relative w-full transition-all duration-300"
            :class="selectedContact ? 'flex' : 'hidden md:flex'">
            <!-- Overlay Loading -->
            <div x-show="loading"
                class="absolute inset-0 bg-white/50 z-50 flex items-center justify-center backdrop-blur-[1px]">
                <div class="w-8 h-8 border-4 border-amber-500 border-t-transparent rounded-full animate-spin"></div>
            </div>

            <!-- Header and Messages will be shown if selectedContact is present -->
            <div x-show="!selectedContact" class="flex-1 flex flex-col items-center justify-center text-gray-500">
                <div
                    class="w-20 h-20 bg-white rounded-3xl flex items-center justify-center mb-4 shadow-sm border border-gray-100">
                    <i data-lucide="shield-alert" class="w-10 h-10 text-amber-200"></i>
                </div>
                <p class="text-lg font-bold text-gray-900">Pusat Bantuan Admin</p>
                <p class="text-sm text-gray-500">Pilih user untuk memberikan bantuan atau monitoring</p>
            </div>

            <div x-show="selectedContact" class="flex-1 flex flex-col overflow-hidden">
                <!-- Chat Header -->
                <div class="p-3 md:p-4 bg-white border-b border-gray-200 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 md:gap-3 min-w-0">
                        <button @click="selectedContact = null" class="md:hidden p-2 -ml-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors" title="Kembali ke daftar">
                            <i data-lucide="arrow-left" class="w-5 h-5"></i>
                        </button>
                        <div :class="selectedContact?.role === 'seller' ? 'bg-emerald-100' : 'bg-emerald-50'"
                            class="w-10 h-10 rounded-xl flex items-center justify-center overflow-hidden">
                            <img x-show="selectedContact?.avatar" :src="selectedContact?.avatar"
                                class="w-full h-full object-cover">
                            <span x-show="!selectedContact?.avatar"
                                :class="selectedContact?.role === 'seller' ? 'text-emerald-700' : 'text-emerald-600'"
                                class="font-bold" x-text="selectedContact?.name?.charAt(0) || '?'"></span>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900" x-text="selectedContact?.name"></h3>
                            <p :class="{
                                                                                                    'text-emerald-700': selectedContact?.role === 'seller',
                                                                                                    'text-amber-600': selectedContact?.role === 'admin' || selectedContact?.role === 'administrator',
                                                                                                    'text-emerald-600': !['seller', 'admin', 'administrator'].includes(selectedContact?.role)
                                                                                                }"
                                class="text-[10px] font-bold uppercase tracking-wider" x-text="selectedContact?.role">
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="clearChat()" title="Hapus Chat & Kontak"
                            class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 active:bg-red-100 rounded-lg transition-all duration-200 relative z-30">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Messages Area -->
                <div id="chat-messages" class="flex-1 overflow-y-auto p-4 md:p-6 space-y-4 custom-scrollbar bg-gray-50/20">
                    <template x-for="msg in messages" :key="msg.id">
                        <div :class="msg.sender_id == window.userId ? 'flex justify-end' : 'flex justify-start'"
                            class="w-full group">
                            <div class="flex flex-col max-w-[85%] md:max-w-[70%]"
                                :class="msg.sender_id == window.userId ? 'items-end' : 'items-start'">

                                <!-- Bubble -->
                                <div :class="msg.sender_id == window.userId ? 'bg-amber-600 text-white rounded-2xl rounded-tr-none shadow-md' : 'bg-white text-gray-800 rounded-2xl rounded-tl-none border border-gray-100 shadow-sm'"
                                    class="px-4 py-3 relative min-w-[80px] transition-all">

                                    <!-- Edit View -->
                                    <template x-if="editingMessage && editingMessage.id === msg.id">
                                        <div class="space-y-2 min-w-[200px]">
                                            <textarea x-model="editContent"
                                                class="w-full bg-black/10 text-white border border-white/20 rounded p-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-200"
                                                rows="2"></textarea>
                                            <div class="flex gap-2 justify-end">
                                                <button @click="cancelEdit()"
                                                    class="text-[10px] uppercase font-bold text-amber-100 hover:text-white">Batal</button>
                                                <button @click="saveEdit(msg)"
                                                    class="text-[10px] uppercase font-bold bg-white text-amber-700 px-2 py-1 rounded hover:bg-amber-50">Simpan</button>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Normal View -->
                                    <template x-if="!editingMessage || editingMessage.id !== msg.id">
                                        <div>
                                            <p class="text-sm leading-relaxed">
                                                <span x-text="msg.message"></span>
                                                <template x-if="msg.is_edited">
                                                    <span class="text-[10px] italic opacity-70 ml-2" :class="msg.sender_id == window.userId ? 'text-amber-100' : 'text-gray-400'">(diedit)</span>
                                                </template>
                                            </p>
                                            <div class="flex items-center justify-between mt-1 gap-4">
                                                <!-- Action Buttons -->
                                                <div class="flex items-center gap-2" x-show="canManage(msg)">
                                                    <button @click="startEdit(msg)" title="Edit"
                                                        :class="msg.sender_id == window.userId ? 'text-amber-100 hover:text-white' : 'text-gray-400 hover:text-amber-600'">
                                                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                                    </button>
                                                    <button @click="deleteMessage(msg)" title="Hapus"
                                                        :class="msg.sender_id == window.userId ? 'text-amber-100 hover:text-white' : 'text-gray-400 hover:text-red-500'">
                                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                    </button>
                                                </div>
                                                <p :class="msg.sender_id == window.userId ? 'text-amber-100' : 'text-gray-400'"
                                                    class="text-[10px] font-medium whitespace-nowrap"
                                                    x-text="formatDate(msg.created_at)"></p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Input Area -->
                <div class="p-3 md:p-4 border-t border-gray-200 bg-white">
                    <div class="flex items-end gap-2 md:gap-3">
                        <div class="flex-1">
                            <textarea x-model="newMessage" @keydown.enter.prevent="sendMessage()"
                                placeholder="Jawab keluhan user..." rows="1"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl resize-none focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm min-h-[46px]"></textarea>
                        </div>
                        <button @click="sendMessage()"
                            class="p-3.5 bg-gradient-to-r from-amber-500 to-amber-600 text-white rounded-xl hover:from-amber-600 hover:to-amber-700 transition-all shadow-lg shadow-amber-100 active:scale-95">
                            <i data-lucide="send" class="w-5 h-5 text-emerald-900"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function chatSystem() {
                return {
                    contacts: [],
                    searchQuery: '',
                    selectedContact: null,
                    messages: [],
                    newMessage: '',
                    pollingInterval: null,
                    loading: false,
                    focusContact: null,
                    editingMessage: null,
                    editContent: '',
                    clearedTimes: JSON.parse(localStorage.getItem('cleared_chats_' + window.userId) || '{}'),
                    hiddenContactIds: JSON.parse(localStorage.getItem('hidden_chats_' + window.userId) || '[]'),

                    // Helper for headers
                    get authHeaders() {
                        return {
                            headers: {
                                'Authorization': 'Bearer ' + window.apiToken,
                                'Accept': 'application/json'
                            }
                        };
                    },

                    async init() {
                        console.log('--- CHAT SYSTEM INIT ---');
                        console.log('Token Length:', window.apiToken ? window.apiToken.length : 0);

                        if (!window.apiToken || window.apiToken === "") {
                            console.error('CRITICAL: API Token is missing from session!');
                            return;
                        }

                        this.loading = true;
                        try {
                            const urlParams = new URLSearchParams(window.location.search);
                            const userIdFromUrl = urlParams.get('user_id');

                            // Jika ada user_id di URL, siapkan focusContact DULU sebelum fetch list
                            if (userIdFromUrl) {
                                try {
                                    const res = await axios.get(window.apiBaseUrl + '/admin/users/' + userIdFromUrl, this.authHeaders);
                                    if (res.data) {
                                        this.focusContact = {
                                            id: res.data.id,
                                            name: res.data.name,
                                            role: res.data.role,
                                            avatar: res.data.profile_image,
                                            last_message: 'Mulai percakapan...',
                                            last_time: '',
                                            unread_count: 0
                                        };
                                    }
                                } catch (e) {
                                    console.error('Failed to pre-fetch user info:', e);
                                }
                            }

                            // Sekarang fetch daftar chat asli
                            await this.fetchContacts();

                            // Jika ada focusContact, pastikan terpilih
                            if (this.focusContact) {
                                await this.selectContact(this.focusContact);
                            } else if (userIdFromUrl) {
                                // Jika pre-fetch gagal tapi ada di list asli
                                let contact = this.contacts.find(c => String(c.id) == String(userIdFromUrl));
                                if (contact) await this.selectContact(contact);
                            }
                        } catch (e) {
                            console.error('Init error:', e);
                        } finally {
                            this.loading = false;
                            this.$nextTick(() => lucide.createIcons());
                        }

                        setInterval(() => this.fetchContacts(), 5000);
                    },

                    async fetchContacts() {
                        try {
                            const response = await axios.get(window.apiBaseUrl + '/chat/list', this.authHeaders);
                            let apiContacts = response.data;
                            apiContacts = apiContacts.map(c => {
                                const clearedAt = this.clearedTimes[c.id];
                                if (clearedAt) {
                                    const clearedDate = new Date(clearedAt);

                                    // AUTO-UNHIDE LOGIC:
                                    // Jika ada pesan baru (last_message_at) yang lebih baru daripada waktu clear lokal
                                    if (c.last_message_at && new Date(c.last_message_at) > clearedDate) {
                                        this.hiddenContactIds = this.hiddenContactIds.filter(id => id != c.id);
                                        this.saveHidden();
                                    } else {
                                        // Still hidden or old history, keep preview clean
                                        c.last_message = '...';
                                    }
                                }
                                return c;
                            });

                            if (this.focusContact) {
                                const found = apiContacts.find(c => c.id == this.focusContact.id);
                                if (!found) {
                                    apiContacts.unshift(this.focusContact);
                                } else {
                                    this.focusContact = null;
                                }
                            }

                            // Keep selected contact in list
                            if (this.selectedContact) {
                                const exists = apiContacts.find(c => c.id == this.selectedContact.id);
                                if (!exists) {
                                    apiContacts.unshift(this.selectedContact);
                                }
                            }

                            this.contacts = apiContacts;
                        } catch (e) {
                            console.error('Polling failed:', e);
                        }
                    },

                    async selectContact(contact) {
                        // Jika memilih user lain yang sudah ada di sidebar, hapus draft "Mulai percakapan" dari user sebelumnya
                        if (this.focusContact && this.focusContact.id != contact.id) {
                            this.focusContact = null;
                            await this.fetchContacts(); // Refresh daftar agar focusContact yang lama hilang
                        }

                        this.selectedContact = contact;

                        // Unhide if hidden
                        this.hiddenContactIds = this.hiddenContactIds.filter(id => id != contact.id);
                        this.saveHidden();

                        await this.fetchMessages(true);

                        // Mark as read
                        if (contact.unread_count > 0) {
                            try {
                                await axios.post(window.apiBaseUrl + '/chat/read/' + contact.id, {}, this.authHeaders);
                                contact.unread_count = 0;
                            } catch (e) {
                                console.error('Failed to mark as read:', e);
                            }
                        }

                        if (this.pollingInterval) clearInterval(this.pollingInterval);
                        this.pollingInterval = setInterval(() => this.fetchMessages(), 3000);

                        this.$nextTick(() => {
                            this.scrollToBottom();
                            lucide.createIcons();
                        });
                    },

                    async fetchMessages(isInitial = false) {
                        if (!this.selectedContact) return;
                        try {
                            const res = await axios.get(window.apiBaseUrl + '/chat/' + this.selectedContact.id, this.authHeaders);
                            let messages = res.data;

                            // Filter locally cleared messages
                            const clearedAt = this.clearedTimes[this.selectedContact.id];
                            if (clearedAt) {
                                const clearedDate = new Date(clearedAt);
                                messages = messages.filter(m => new Date(m.created_at) > clearedDate);
                            }

                            const oldLength = this.messages.length;
                            this.messages = messages;
                            this.$nextTick(() => {
                                if (isInitial || oldLength === 0 || this.messages.length > oldLength || this.shouldScrollToBottom()) {
                                    this.scrollToBottom();
                                }
                            });
                        } catch (e) { }
                    },

                    async sendMessage() {
                        if (!this.newMessage.trim() || !this.selectedContact) return;
                        const text = this.newMessage;
                        const receiverId = this.selectedContact.id;
                        this.newMessage = '';

                        try {
                            await axios.post(window.apiBaseUrl + '/chat/send', {
                                receiver_id: receiverId,
                                message: text
                            }, this.authHeaders);
                            this.focusContact = null;
                            await this.fetchMessages();
                            await this.fetchContacts();
                        } catch (e) {
                            console.error('Send error:', e);
                        }
                    },

                    get filteredContacts() {
                        return this.contacts.filter(c => {
                            const matchSearch = c.name && c.name.toLowerCase().includes(this.searchQuery.toLowerCase());
                            const isHidden = this.hiddenContactIds.includes(c.id);
                            // Show if match search AND (not hidden OR specifically searching)
                            return matchSearch && (!isHidden || this.searchQuery.length > 0);
                        });
                    },

                    shouldScrollToBottom() {
                        const el = document.getElementById('chat-messages');
                        return el ? (el.scrollHeight - el.scrollTop - el.clientHeight < 200) : false;
                    },

                    scrollToBottom() {
                        const el = document.getElementById('chat-messages');
                        if (el) el.scrollTop = el.scrollHeight;
                    },

                    async deleteMessage(msg) {
                        try {
                            const res = await Swal.fire({
                                title: 'Hapus pesan?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, Hapus',
                                confirmButtonColor: '#ef4444',
                                customClass: { popup: 'rounded-2xl' }
                            });
                            if (!res.isConfirmed) return;

                            await axios.delete(window.apiBaseUrl + '/chat/message/' + msg.id, this.authHeaders);
                            this.fetchMessages();
                        } catch (e) {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: e.response?.data?.message || 'Gagal menghapus pesan', customClass: { popup: 'rounded-2xl' } });
                        }
                    },

                    async clearChat() {
                        if (!this.selectedContact) return;
                        try {
                            const res = await Swal.fire({
                                title: 'Bersihkan Obrolan?',
                                text: 'Apakah Anda yakin ingin menghapus riwayat obrolan ini dari daftar Anda?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, Bersihkan',
                                cancelButtonText: 'Batal',
                                confirmButtonColor: '#d97706', /* Amber 600 */
                                customClass: { popup: 'rounded-2xl' }
                            });
                            if (!res.isConfirmed) return;

                            const contactId = this.selectedContact.id;
                            // Set cleared time to NOW and add to hidden
                            this.clearedTimes[contactId] = new Date().toISOString();
                            if (!this.hiddenContactIds.includes(contactId)) {
                                this.hiddenContactIds.push(contactId);
                            }
                            this.saveCleared();
                            this.saveHidden();

                            this.messages = [];
                            this.selectedContact = null;
                            await this.fetchContacts();

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Percakapan telah dibersihkan.',
                                showConfirmButton: false,
                                timer: 1500,
                                customClass: { popup: 'rounded-2xl' }
                            });
                        } catch (e) {
                            console.error('Clear chat error:', e);
                        }
                    },

                    startEdit(msg) {
                        this.editingMessage = msg;
                        this.editContent = msg.message;
                        this.$nextTick(() => lucide.createIcons());
                    },

                    cancelEdit() {
                        this.editingMessage = null;
                        this.editContent = '';
                    },

                    async saveEdit(msg) {
                        if (!this.editContent.trim()) return;
                        try {
                            await axios.put(window.apiBaseUrl + '/chat/message/' + msg.id, { message: this.editContent }, this.authHeaders);
                            this.editingMessage = null;
                            this.editContent = '';
                            this.fetchMessages();
                        } catch (e) {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: e.response?.data?.message || 'Gagal mengedit pesan', customClass: { popup: 'rounded-2xl' } });
                        }
                    },

                    canManage(msg) {
                        if (msg.sender_id != window.userId) return false;
                        let dateStr = msg.created_at;
                        if (!dateStr.endsWith('Z')) {
                            dateStr = dateStr.replace(' ', 'T') + 'Z';
                        }
                        const created = new Date(dateStr);
                        const now = new Date();
                        const diff = (now - created) / 1000 / 60; // in minutes
                        return diff < 5;
                    },

                    async deleteConversationLocally() {
                        if (!this.selectedContact) return;
                        const contactId = this.selectedContact.id;

                        const res = await Swal.fire({
                            title: 'Bersihkan Obrolan?',
                            text: 'Apakah Anda yakin ingin menghapus riwayat obrolan ini dari daftar Anda?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Bersihkan',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: '#d97706', /* Amber 600 */
                            customClass: { popup: 'rounded-2xl' }
                        });

                        if (res.isConfirmed) {
                            // Set cleared time to NOW and add to hidden
                            this.clearedTimes[contactId] = new Date().toISOString();
                            if (!this.hiddenContactIds.includes(contactId)) {
                                this.hiddenContactIds.push(contactId);
                            }
                            this.saveCleared();
                            this.saveHidden();

                            this.selectedContact = null;
                            this.messages = [];
                            await this.fetchContacts();
                        }
                    },

                    saveCleared() {
                        localStorage.setItem('cleared_chats_' + window.userId, JSON.stringify(this.clearedTimes));
                    },

                    saveHidden() {
                        localStorage.setItem('hidden_chats_' + window.userId, JSON.stringify(this.hiddenContactIds));
                    },

                    async deleteMessage(msg) {
                        try {
                            const res = await Swal.fire({
                                title: 'Hapus pesan?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, Hapus',
                                confirmButtonColor: '#ef4444',
                                customClass: { popup: 'rounded-2xl' }
                            });
                            if (!res.isConfirmed) return;

                            await axios.delete(window.apiBaseUrl + '/chat/message/' + msg.id, this.authHeaders);
                            this.fetchMessages();
                        } catch (e) {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal menghapus pesan', customClass: { popup: 'rounded-2xl' } });
                        }
                    },

                    formatDate(dateStr) {
                        if (!dateStr) return '';
                        const d = new Date(dateStr);
                        return d.getHours().toString().padStart(2, '0') + ':' + d.getMinutes().toString().padStart(2, '0');
                    }
                }
            }
        </script>
    @endpush
@endsection