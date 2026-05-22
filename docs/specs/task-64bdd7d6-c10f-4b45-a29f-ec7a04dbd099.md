# 功能規格：修復 Laravel 路由全面 404 問題排查與修正

## 問題描述

使用者回報所有路由皆回傳 404，代表應用程式完全無法正確匹配任何路由。此問題通常源於以下原因：

1. **未正確設定 `public/.htaccess`（Apache）或 Nginx 設定遺漏 `try_files`**
2. **`public/index.php` 路由載入失敗**
3. **Laravel 路由檔案未正確載入**

## 排查步驟

### 1. 確認 `public/.htaccess`

Apache 必須有正確的 `.htaccess` 檔案，將所有請求重寫到 `index.php`。

### 2. 確認 `routes/web.php` 存在且有內容

### 3. 確認 `bootstrap/cache` 可寫入

### 4. 確認 `mod_rewrite` 已啟用

## 修正方案

### 檔案清單

| 檔案路徑 | 說明 |
|---|---|
| `public/.htaccess` | Apache 重寫規則，確保所有請求導向 `index.php` |
| `routes/web.php` | 確認路由檔案存在 |
| `bootstrap/cache` | 確認目錄可寫入 |

## 驗證方式

1. 存取首頁 `/` 應回傳 200
2. 存取 `/login` 應回傳 200 或 302
3. 存取不存在的路由應回傳 404（而非所有路由都 404）