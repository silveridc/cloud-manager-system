<script setup>
import { ref } from 'vue'
import { LockOutlined, ApiOutlined } from '@ant-design/icons-vue'
import { login } from '../api'
import { message } from 'ant-design-vue'

const emit = defineEmits(['login-success'])
const password = ref('')
const loading = ref(false)

async function handleLogin() {
  if (!password.value) {
    message.warning('请输入访问密码')
    return
  }
  loading.value = true
  try {
    const res = await login(password.value)
    if (res.status == 200) {
      message.success('登录成功')
      emit('login-success')
    } else {
      message.error(res.message || '密码错误')
    }
  } catch (e) {
    message.error('请求失败，请稍后重试')
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <ApiOutlined class="login-icon" />
        <h1>API 接口文档</h1>
        <p class="login-subtitle">请输入访问密码以继续</p>
      </div>
      <a-form @finish="handleLogin" layout="vertical">
        <a-form-item>
          <a-input-password
            v-model:value="password"
            size="large"
            placeholder="请输入访问密码"
            @pressEnter="handleLogin"
          >
            <template #prefix>
              <LockOutlined style="color: rgba(0, 0, 0, 0.25)" />
            </template>
          </a-input-password>
        </a-form-item>
        <a-form-item>
          <a-button
            type="primary"
            html-type="submit"
            size="large"
            block
            :loading="loading"
          >
            进入文档
          </a-button>
        </a-form-item>
      </a-form>
    </div>
  </div>
</template>

<style scoped>
.login-container {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.login-card {
  width: 400px;
  padding: 48px 40px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

.login-header {
  text-align: center;
  margin-bottom: 32px;
}

.login-icon {
  font-size: 48px;
  color: #667eea;
  margin-bottom: 16px;
}

.login-header h1 {
  font-size: 24px;
  font-weight: 600;
  color: #1a1a2e;
  margin: 0 0 8px 0;
}

.login-subtitle {
  color: #666;
  font-size: 14px;
}
</style>
