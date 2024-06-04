# API實作測驗
https://github.com/jack100398/CurrencyExchange

# 資料庫測驗
1.
```
SELECT bnbs.id, bnb_name, sum(amount) as may_amount
FROM orders
JOIN bnbs on orders.bnb_id = bnbs.id 
WHERE orders.currency = 'TWD'
AND orders.created_at BETWEEN '2023-05-01' AND '2023-05-31'
GROUP BY bnb_id, bnb_name
ORDER BY may_amount DESC
LIMIT 10
```

2.
- Query速度慢的原因應該會出在 orders 表, 由於這張表應該會包含`所有`的訂單 資料量體會非常大
- Explain Query 先檢查搜尋的細節 索引的使用狀況 掃描過的 row 數量...做分析
- 最直觀的方法會先確認是否有索引
- 會考慮用 subQuery / WITH 的方式 先將 orders 的結果算出來之後再進好 join, 減少資料量體後再進行join有機會提升部分速度

# 經驗分享
1.
過往經驗比較常意識到的應該屬於 SRP / DIP
SRP的部分, 我認為不僅僅是在一個類別上, 在單一方法上也常常在思考這個方法實際上做了什麼 負責的事情會不會太多
如果堅定地維持這個規範, 在流程上非常的清楚, 每一個環節會做什麼樣的事情一目瞭然 但問題就是 類別越開越多/方法越寫越多

DIP的部分曾經實作一個 Api Cache 的功能, 配合資料同步(資料源一天刷新一次), 每天會執行排程將可預期參數的 Api 回傳進行快取
這部分也是有定義一個 interface 裡面規範必需要實作 getParameter 的方法, 每一個需要被快取的 Api 需要取得實作當日要跑的參數
後續如果有新的Api需要被快取, 只要引用這個Interface 並實作, 都可以直接加入快取的行列

SOLID 主要是使用`物件導向`設計上做開法的五個準則, 主要能避免 改A壞B, 需求或功能調整後 由於互相耦合的原因 導致有改不動或類別過於龐大等等的狀況發生

DIP的部分我認為在實作測驗的題目是可以做說明的, 實作上 service 依賴 interface/currency 而不是 單一幣值(如: TWD)
這樣未來如果有更多的幣值開放使用, 只要建立好新的幣值的類別 並且 實作的方法有遵守 interface 規範的`實際用途與意義` , 即可直接套用

ISP的部分主要是避免 interface, 規範引用的類別需要時做`太多``不需要`的方法
比較實際的例子像是 以 `人` 來說 人可以有許多的技能 像是 修理汽車/游泳/吃飯...
但如果因為這樣我就認為所有人都該擁有這些技能, 並且規範人都應該有這些實作, 一切會變得很詭異
像是在路上遇到的每一個人都會修理汽車, 但也許他們並不真的需要這樣的技能, 所以會需要將 interface 切開
human 只做定義的基本的 如 呼吸/喝水..., carFixer才定義 修理汽車的方法
這樣在實作子類別的時候壓力會比較小, 只引用human的時候 實際上依然是一個`人`, 但不再需要實作修理汽車的方法 導致過度開發的狀況

2.
Functional Programming 主要是在做方法的任務拆分, 將一個方法拆成很多的小方法, 這樣每個小方法的行為會變得單純
結果就是同樣的方法可能可以被其他的任務所使用, 並且在閱覽與維護上也會更加清楚
Object-oriented Programming 主要在做功能的定義, 定義一個類別擁有哪些功能, 可以完成什麼樣的事情
那我想兩個東西應該是可以一起使用並且是相輔相成的, 畢竟我們也不會希望使用OOP的時候 看到一個方法寫了一百行造成難以閱讀或調整的狀況

class比較偏向規格書, 定義這個`類別`所建立出來的`實體`應該要有什麼樣的功能

3.
如果我們定義了一個抽象類別, 並且沒有任何的實作, 在接口上引用 確實和 interface 差別不大
但一個 class 僅可以 extends 一個 abstract class, 卻可以 implements 多個 interface
也就是說 如果有一個注入點 需要的功能分別在兩個 interface 內
如 汽車修理工 可能需要 human and carFix, 就可以有一個 抽象類別 implements human, carFix
當然我依然可以選擇不使用 implements 直接在 abstract class 內定義需要的方法, 但後續面臨的狀況就是
如果這個類別有其他的分支或不同的專業產生, 在實作子類別的時候可能又會違反ISP原則

4.
Contrainer 是容器, Service Provider 是服務提供者, 可以理解為 Service Provider 提供的服務會被放置在容器中, 需要的時候可以直接取用
主要是預先建立一些類別 或者定義一個類別產生的過程預先處理
比較常看到的像依賴注入時, ServiceProvider 可以定義 不同的類別注入某類別時, 所需要實例化的實體究竟是哪一個 也能定義注入的物件是相同的或者全新的一個實體
避免可能到處都在 new 類似的實體, 減少一些開發上的麻煩或困擾

5.
__invoke 是 PHP 的魔術方法 當將這個類別當作方法呼叫的時候就會觸發
比較實際的例子是像 Route 可以這樣寫, 這樣就會觸發 CurrencyExchangeController 的 ＿＿invoke
```
Route::get('currency-exchange', CurrencyExchangeController::class);
```

6.
接縫指的是可以被抽換功能的地方
依實作測驗的地方來說 Service 的地方依賴注入一個 interface, 那在測試中就可以透過 mock 一個假的類別注入 這個 Service 裡面
這樣Service有使用到這個類別的部分的回傳就可以被預期且是可控的