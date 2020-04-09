#!/usr/bin/python

import sqlite3
import openpyxl
import cv2
import glob
import os
from ocr import *
	
conn = sqlite3.connect('data.db')
print ("Opened database successfully")
c = conn.cursor()

c.execute('''CREATE TABLE IF NOT EXISTS PERSONNEL
      (单位			  TEXT    NOT NULL,
	   简称           TEXT    NOT NULL,
	   合同号         TEXT    NOT NULL,
	   岗位           TEXT    ,
	   姓名			  TEXT    NOT NULL,
	   身份证号	TEXT  PRIMARY KEY NOT NULL,
	   返岗日期		  DATE    ,
	   轨迹			  TEXT    NOT NULL,
	   人证验证		  TEXT    );''')
print ("Table created successfully")
conn.commit()

company = {'S231KS-SG1':'南通路桥工程有限公司',
		   'S231KS-SG2':'江苏省邗江交通建设工程有限公司',
		   'S231KS-SG3':'江苏润扬交通工程集团有限公司',
		   'S231KS-JL1':'江苏兆信工程项目管理有限公司',
		   'S231KS-JL2':'南京交通建设项目管理有限责任公司',
		   'S231KS-JC':'苏交科集团股份有限公司',
		   'DHBY-JA':'江苏中泰建发集团有限公司',
		   'DHBY-JD':'江苏中泰建发集团有限公司',
		   'DHBY-LH1':'江苏中泰建发集团有限公司',
		   'DHBY-ZMSG':'江苏时新景观照明有限公司',
		   'ZQLKS-SG1':'江苏省镇江市路桥工程总公司',
		   'ZQLKS-SG2':'江苏省交通工程集团有限公司',
		   'ZQLKS-SG3':'南通路桥工程有限公司',
		   'ZQLKS-SG4':'江苏港通路桥集团有限公司',
		   'ZQLKS-SG5':'北京市政建设集团有限责任公司',
		   'ZQLKS-SG6':'江苏金领建设发展有限公司',
		   'ZQLKS-JL1':'江苏华宁工程咨询监理有限公司',
		   'ZQLKS-JL2':'江苏兆信工程项目管理有限公司',
		   'ZQLKS-JC':'江苏辉通检测有限公司',
		   'ZQLKS-JA1':'江苏中泰建发集团有限公司',
		   'ZQLKS-JD1':'江苏中泰建发集团有限公司',
		   'ZQLKS-LH1':'江苏中泰建发集团有限公司',
		   'ZQLKS-ZMSG':'神州交通工程集团有限公司',
		   'DHBY-JDJL':'江苏智远交通科技有限公司',
		   'ZQLKS-JDJL':'江苏智远交通科技有限公司',
		   'ZHB':'泰州市市区公路工程建设指挥部'}
scompany = {'S231KS-SG1':'南通路桥',
		   'S231KS-SG2':'邗江交建',
		   'S231KS-SG3':'江苏润扬',
		   'S231KS-JL1':'江苏兆信',
		   'S231KS-JL2':'南京交建',
		   'S231KS-JC':'苏交科',
		   'DHBY-JA':'中泰建发',
		   'DHBY-JD':'中泰建发',
		   'DHBY-LH1':'中泰建发',
		   'DHBY-ZMSG':'时新照明',
		   'ZQLKS-SG1':'镇江路桥',
		   'ZQLKS-SG2':'江苏交工',
		   'ZQLKS-SG3':'南通路桥',
		   'ZQLKS-SG4':'港通路桥',
		   'ZQLKS-SG5':'北京市政',
		   'ZQLKS-SG6':'江苏金领',
		   'ZQLKS-JL1':'江苏华宁',
		   'ZQLKS-JL2':'江苏兆信',
		   'ZQLKS-JC':'辉通检测',
		   'ZQLKS-JA1':'中泰建发',
		   'ZQLKS-JD1':'中泰建发',
		   'ZQLKS-LH1':'中泰建发',
		   'ZQLKS-ZMSG':'神州交通',
		   'DHBY-JDJL':'江苏智远',
		   'ZQLKS-JDJL':'江苏智远',
		   'ZHB':'市区指'}
		   
xlsFile = openpyxl.load_workbook('人员进场表.xlsx')
#xlsFileSheet = xlsFile.get_sheet_by_name("明细")
xlsFileSheet = xlsFile["明细"]
for row in xlsFileSheet.iter_rows(min_row=3):
	#Company = row[1]
	#SCompany =
	#for cell in row:
	#	print(cell.value)
	#print(row[2].value)
	Contract = row[2].value
	#print(Contract)
	Company = company[Contract]
	SCompany = scompany[Contract]
	Title = row[3].value
	Name = row[4].value
	NO = str(row[5].value).replace('X','x')
	Date = row[6].value
	Track = row[7].value
	#print(Track)
	IDName = './ID-CARDS/' + Contract +'/' + Name + '.*' 
	#print(NO)
	IDPath = glob.glob(IDName)
	
	if len(IDPath) == 0: 
		Verify = '无身份证'
		print(NO, Verify)
	else: 
		img = cv2.imread(IDPath[0])
		NO2 = ocrIdCard(IDPath[0])
		if NO2 == NO : Verify = '验证通过'
		else: Verify = '人工验证'
		print(NO, NO2, Verify)
	#print(IDName, Verify)
	#Verify = ''
	c.execute("INSERT OR REPLACE INTO PERSONNEL (单位,简称,合同号,岗位,姓名,身份证号,返岗日期,轨迹,人证验证) VALUES('%s','%s','%s','%s','%s','%s','%s','%s','%s')"%(Company, SCompany, Contract, Title, Name, NO, Date, Track, Verify))
	
conn.commit()
print ("Records created successfully")

for row in c.execute("SELECT COUNT('身份证号') FROM PERSONNEL"):
	print("总人数：", row[0], type(row[0]))

conn.close()