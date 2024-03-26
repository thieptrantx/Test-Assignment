<h1>Test 1 - MySQL: Query</h1>
<hr />
<h2>MySQL: Query</h2>
<p>Table tbl_contents have 1M records with columns: id, cate_id, title, content, published(0,1), created_at, updated_at (model Content)</p>
<p>Table tbl_categories have 1M records with columns: id, name, published(0,1), created_at, updated_at (model Category)</p>
<ul>
	<li>1. Write a php script to find all the contents (title or content) with matching the keyword "Computer on Module" are published (1), order by updated_at, limit 100 records.</li>
	<li>2. Write a php script to find all the contents with matching the category "CPU" are published (1), order by updated_at, limit 100 records.</li>
	<li>3. Please explain which fields in database need to make indexes.</li>
</ul>
<!-- Write your answer below -->

<h2>MySQL: Master and Slaves</h2>
<p>We have 4 servers: www1, www2, www3, www-cn</p>
<ul>
	<li>1. e.g www1 is master, other servers are slaves. Please explain how can configure for master and slaves.</li>
	<li>2. Do you have any other solutions without using master and slaves and make our server databases always have latest data?</li>
</ul>
<!-- Write your answer below -->