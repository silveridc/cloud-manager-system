<script setup>
import { ref, onMounted } from 'vue'
import { getList } from '../api'
import { ApiOutlined, FileTextOutlined, FolderOpenOutlined, SearchOutlined } from '@ant-design/icons-vue'
import ApiDetail from './ApiDetail.vue'
import SearchPanel from './SearchPanel.vue'

const treeData = ref([])
const activeKey = ref('search')
const tabs = ref([{ key: 'search', title: '搜索', closable: false }])
const currentApi = ref(null)
const loading = ref(false)

function transformTree(list) {
  return list.map(item => {
    const node = {
      title: item.title || '未命名',
      key: item.name || item.title || Math.random().toString(),
      isLeaf: !item.actions || item.actions.length === 0,
      raw: item,
    }
    if (item.actions && item.actions.length > 0) {
      node.children = transformTree(item.actions)
    }
    return node
  })
}

onMounted(async () => {
  loading.value = true
  try {
    const data = await getList()
    treeData.value = transformTree(data.list || [])
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})

function onSelect(selectedKeys, { node }) {
  if (!node.isLeaf) return
  const name = node.key
  const title = node.title
  const existing = tabs.value.find(t => t.key === name)
  if (!existing) {
    tabs.value.push({ key: name, title, closable: true })
  }
  activeKey.value = name
  currentApi.value = name
}

function onTabEdit(targetKey, action) {
  if (action === 'remove') {
    const idx = tabs.value.findIndex(t => t.key === targetKey)
    tabs.value.splice(idx, 1)
    if (activeKey.value === targetKey) {
      activeKey.value = tabs.value[tabs.value.length - 1]?.key || 'search'
    }
  }
}

function onSearchSelect(name, title) {
  const existing = tabs.value.find(t => t.key === name)
  if (!existing) {
    tabs.value.push({ key: name, title, closable: true })
  }
  activeKey.value = name
}
</script>

<template>
  <a-layout class="main-layout">
    <a-layout-sider width="280" class="sidebar" theme="light">
      <div class="sidebar-header">
        <ApiOutlined />
        <span>API 文档</span>
      </div>
      <div class="sidebar-tree">
        <a-spin :spinning="loading" tip="加载中...">
          <a-tree
            v-if="treeData.length"
            :tree-data="treeData"
            :default-expand-all="true"
            :show-icon="true"
            @select="onSelect"
          >
            <template #icon="{ isLeaf }">
              <FileTextOutlined v-if="isLeaf" />
              <FolderOpenOutlined v-else />
            </template>
          </a-tree>
        </a-spin>
      </div>
    </a-layout-sider>
    <a-layout>
      <a-layout-content class="content-area">
        <a-tabs
          v-model:activeKey="activeKey"
          type="editable-card"
          hide-add
          @edit="onTabEdit"
          class="content-tabs"
        >
          <a-tab-pane
            v-for="tab in tabs"
            :key="tab.key"
            :tab="tab.title"
            :closable="tab.closable"
          >
            <SearchPanel v-if="tab.key === 'search'" @select="onSearchSelect" />
            <ApiDetail v-else :name="tab.key" />
          </a-tab-pane>
        </a-tabs>
      </a-layout-content>
    </a-layout>
  </a-layout>
</template>

<style scoped>
.main-layout {
  height: 100%;
}

.sidebar {
  border-right: 1px solid #f0f0f0;
  overflow-y: auto;
}

.sidebar-header {
  padding: 16px 20px;
  font-size: 18px;
  font-weight: 600;
  color: #1a1a2e;
  border-bottom: 1px solid #f0f0f0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.sidebar-tree {
  padding: 8px;
  overflow-y: auto;
  height: calc(100% - 57px);
}

.content-area {
  padding: 0;
  height: 100%;
  overflow: hidden;
}

.content-tabs {
  height: 100%;
}

.content-tabs :deep(.ant-tabs-content) {
  height: calc(100% - 46px);
  overflow-y: auto;
  padding: 16px 24px;
}
</style>
