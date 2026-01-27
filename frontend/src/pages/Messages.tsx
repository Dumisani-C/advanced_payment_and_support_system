import { useEffect, useState, useRef } from 'react';
import { getConversations, getConversation, sendMessage, markMessagesRead } from '../lib/api';
import { MessageCircle, Send, ArrowLeft, Loader2, Search, MoreHorizontal } from 'lucide-react';

interface Conversation {
  other_user_id: number;
  other_user_name: string;
  other_user_type: string;
  last_message_time: string;
  unread_count: number;
  last_message: string;
}

interface Message {
  id: number;
  sender_id: number;
  receiver_id: number;
  message: string;
  date_created: string;
  sender_name: string;
  apartment_no?: string;
}

export default function Messages() {
  const user = JSON.parse(localStorage.getItem('user') || '{}');
  const [conversations, setConversations] = useState<Conversation[]>([]);
  const [messages, setMessages] = useState<Message[]>([]);
  const [selectedUser, setSelectedUser] = useState<Conversation | null>(null);
  const [newMessage, setNewMessage] = useState('');
  const [loading, setLoading] = useState(true);
  const [sending, setSending] = useState(false);
  const messagesEndRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    fetchConversations();
  }, []);

  useEffect(() => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [messages]);

  const fetchConversations = async () => {
    try {
      const response = await getConversations(user.id);
      setConversations(response.data);
    } catch (error) {
      console.error('Failed to fetch conversations:', error);
    } finally {
      setLoading(false);
    }
  };

  const selectConversation = async (conv: Conversation) => {
    setSelectedUser(conv);
    setLoading(true);
    try {
      const response = await getConversation(user.id, conv.other_user_id);
      setMessages(response.data);
      // Mark as read
      if (conv.unread_count > 0) {
        await markMessagesRead(conv.other_user_id, user.id);
        fetchConversations();
      }
    } catch (error) {
      console.error('Failed to fetch messages:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleSend = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!newMessage.trim() || !selectedUser) return;

    setSending(true);
    try {
      await sendMessage({
        sender_id: user.id,
        receiver_id: selectedUser.other_user_id,
        message: newMessage.trim(),
      });
      setNewMessage('');
      // Refresh messages
      const response = await getConversation(user.id, selectedUser.other_user_id);
      setMessages(response.data);
      fetchConversations();
    } catch (error) {
      console.error('Failed to send message:', error);
      alert('Failed to send message');
    } finally {
      setSending(false);
    }
  };

  const formatTime = (dateStr: string) => {
    const date = new Date(dateStr);
    const now = new Date();
    const diff = now.getTime() - date.getTime();
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    
    if (days === 0) {
      return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
    } else if (days === 1) {
      return 'Yesterday';
    } else if (days < 7) {
      return date.toLocaleDateString('en-US', { weekday: 'short' });
    }
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
  };

  return (
    <div className="h-[calc(100vh-120px)] flex bg-white rounded-xl border border-slate-200 overflow-hidden">
      {/* Conversations List */}
      <div className={`w-full md:w-80 border-r border-slate-200 flex flex-col ${selectedUser ? 'hidden md:flex' : ''}`}>
        {/* Header */}
        <div className="p-4 border-b border-slate-200">
          <div className="flex items-center justify-between mb-3">
            <h2 className="text-lg font-semibold text-slate-900">Messages</h2>
            <button className="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg">
              <MoreHorizontal className="w-5 h-5" />
            </button>
          </div>
          <div className="relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
            <input
              type="text"
              placeholder="Search messages..."
              className="w-full pl-9 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
            />
          </div>
        </div>

        {/* Conversations */}
        <div className="flex-1 overflow-y-auto">
          {loading && conversations.length === 0 ? (
            <div className="flex items-center justify-center py-12">
              <Loader2 className="w-6 h-6 animate-spin text-indigo-600" />
            </div>
          ) : conversations.length > 0 ? (
            conversations.map((conv) => (
              <button
                key={conv.other_user_id}
                onClick={() => selectConversation(conv)}
                className={`w-full p-3 text-left hover:bg-slate-50 transition-colors border-b border-slate-100 ${
                  selectedUser?.other_user_id === conv.other_user_id ? 'bg-indigo-50 border-l-2 border-l-indigo-600' : ''
                }`}
              >
                <div className="flex items-start gap-3">
                  <div className="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center flex-shrink-0 text-white text-sm font-semibold">
                    {conv.other_user_name?.charAt(0) || '?'}
                  </div>
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center justify-between">
                      <p className="text-sm font-medium text-slate-900 truncate">{conv.other_user_name}</p>
                      <span className="text-xs text-slate-400">{formatTime(conv.last_message_time)}</span>
                    </div>
                    <div className="flex items-center justify-between mt-0.5">
                      <p className="text-sm text-slate-500 truncate">{conv.last_message}</p>
                      {conv.unread_count > 0 && (
                        <span className="ml-2 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 bg-indigo-600 text-white text-xs font-medium rounded-full">
                          {conv.unread_count}
                        </span>
                      )}
                    </div>
                    <p className="text-xs text-slate-400 mt-1">
                      {conv.other_user_type === '3' ? 'Landlord' : 'Tenant'}
                    </p>
                  </div>
                </div>
              </button>
            ))
          ) : (
            <div className="flex flex-col items-center justify-center py-12 px-4 text-center">
              <div className="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mb-3">
                <MessageCircle className="w-6 h-6 text-slate-400" />
              </div>
              <p className="text-sm font-medium text-slate-900">No conversations yet</p>
              <p className="text-sm text-slate-500 mt-1">
                Start by enquiring about a property
              </p>
            </div>
          )}
        </div>
      </div>

      {/* Chat Area */}
      <div className={`flex-1 flex flex-col ${!selectedUser ? 'hidden md:flex' : ''}`}>
        {selectedUser ? (
          <>
            {/* Chat Header */}
            <div className="p-4 border-b border-slate-200 flex items-center justify-between">
              <div className="flex items-center gap-3">
                <button
                  onClick={() => setSelectedUser(null)}
                  className="md:hidden p-2 hover:bg-slate-100 rounded-lg text-slate-500"
                >
                  <ArrowLeft className="w-5 h-5" />
                </button>
                <div className="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-white text-sm font-semibold">
                  {selectedUser.other_user_name?.charAt(0) || '?'}
                </div>
                <div>
                  <p className="font-medium text-slate-900">{selectedUser.other_user_name}</p>
                  <p className="text-xs text-slate-500 flex items-center gap-1">
                    <span className="w-2 h-2 bg-emerald-500 rounded-full"></span>
                    {selectedUser.other_user_type === '3' ? 'Landlord' : 'Tenant'}
                  </p>
                </div>
              </div>
              <button className="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg">
                <MoreHorizontal className="w-5 h-5" />
              </button>
            </div>

            {/* Messages */}
            <div className="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50">
              {loading ? (
                <div className="flex items-center justify-center py-12">
                  <Loader2 className="w-6 h-6 animate-spin text-indigo-600" />
                </div>
              ) : messages.length > 0 ? (
                messages.map((msg) => (
                  <div
                    key={msg.id}
                    className={`flex ${msg.sender_id === user.id ? 'justify-end' : 'justify-start'}`}
                  >
                    <div
                      className={`max-w-[70%] rounded-2xl px-4 py-2.5 ${
                        msg.sender_id === user.id
                          ? 'bg-indigo-600 text-white rounded-br-sm'
                          : 'bg-white text-slate-900 shadow-sm border border-slate-200 rounded-bl-sm'
                      }`}
                    >
                      <p className="text-sm whitespace-pre-wrap">{msg.message}</p>
                      <p className={`text-xs mt-1 ${msg.sender_id === user.id ? 'text-indigo-200' : 'text-slate-400'}`}>
                        {formatTime(msg.date_created)}
                      </p>
                    </div>
                  </div>
                ))
              ) : (
                <div className="text-center py-8">
                  <p className="text-sm text-slate-500">No messages yet. Start the conversation!</p>
                </div>
              )}
              <div ref={messagesEndRef} />
            </div>

            {/* Message Input */}
            <form onSubmit={handleSend} className="p-4 border-t border-slate-200 bg-white">
              <div className="flex gap-3">
                <input
                  type="text"
                  value={newMessage}
                  onChange={(e) => setNewMessage(e.target.value)}
                  placeholder="Type your message..."
                  className="flex-1 px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                />
                <button
                  type="submit"
                  disabled={sending || !newMessage.trim()}
                  className="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors disabled:opacity-50 flex items-center gap-2"
                >
                  {sending ? (
                    <Loader2 className="w-5 h-5 animate-spin" />
                  ) : (
                    <Send className="w-5 h-5" />
                  )}
                </button>
              </div>
            </form>
          </>
        ) : (
          <div className="flex-1 flex items-center justify-center bg-slate-50">
            <div className="text-center">
              <div className="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <MessageCircle className="w-8 h-8 text-slate-400" />
              </div>
              <p className="text-sm font-medium text-slate-900">Select a conversation</p>
              <p className="text-sm text-slate-500 mt-1">Choose from your messages on the left</p>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
