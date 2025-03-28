import pyautogui
import time

# เปิดโปรแกรม myAccount
pyautogui.hotkey('win', 'r')  # เปิด Run dialog
pyautogui.write('myAccount')  # พิมพ์ชื่อโปรแกรม
pyautogui.press('enter')  # กด Enter
time.sleep(2)  # รอให้โปรแกรมเปิดขึ้น

# จำลองการพิมพ์ข้อมูลลงในฟอร์มของโปรแกรม
pyautogui.write('ข้อมูลที่ต้องการคัดลอกไปยังโปรแกรม')
pyautogui.press('tab')  # ถ้าต้องการย้ายไปยังฟิลด์ถัดไป
pyautogui.write('ข้อมูลที่สอง')
pyautogui.press('enter')  # กด Enter เพื่อส่งข้อมูล

import json

# ข้อมูลที่ต้องการคัดลอก
data = {'username': 'user1', 'password': 'password123'}

# สร้างไฟล์ JSON
with open('data.json', 'w') as file:
    json.dump(data, file)

import pyperclip

# คัดลอกข้อมูลไปยังคลิปบอร์ด
pyperclip.copy('ข้อมูลที่ต้องการคัดลอก')

# วางข้อมูลจากคลิปบอร์ดไปยังโปรแกรมที่ต้องการ
# ใช้ PyAutoGUI หรือเครื่องมืออื่น ๆ เพื่อวางข้อมูลในโปรแกรม
