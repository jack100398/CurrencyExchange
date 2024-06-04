# Install
1. Copy Env File
```shell
cp .env.example .env
```
2. Up Docker Container
```shell
docker compose up -d
```
3. Install Packages
```shell
docker compose exec backend composer i
```
4. Generate Laravel App Key
```shell
php artisan key:generate
```
5. Close Contain
```shell
docker compose down
```
---
# Usage
- Api Url
```
127.0.0.1/api/currency-exchange?source=TWD&target=USD&amount=0
```
- Run Test
```shell
php artisan test
```

# Detail
- Enum 管理合法的 幣值項目
- 使用 FormRequest 的 prepareForValidation 預先轉換 `amount` 去除千分位 再進行驗證
- app/Interface/Currency 規範 幣值類別需要實作取得匯率方法
- Service 依賴 app/Interface/Currency 由 Enum 配合 使用者輸入決定注入的幣值物件
- 謝謝您的閱覽與審查