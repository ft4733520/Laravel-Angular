

	# 晓乎后端API文档 V1.0.0
	## 常规API调用原则
	- 所有的API都以'domain.com/api/..'开头
	- API分为两个部分，如'domain.com/api/part_1/part_2'
		- 'part_1'为model名称，如'user'或'question'
		- 'part_2'为行为的名称,如'reset_password'
	- CURD
		- 每个model中都会有增删改查四个方法，分别对应为'add'、'remove'、'change'、'read'

	#Model