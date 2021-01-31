
import mysql.connector
from mysql.connector import Error
from mysql.connector import errorcode

import string 
import sys 
import docx
import sys


import datetime

connection = mysql.connector.connect(host='localhost',database='plag',user='extra', password='Qwerty12!@')

# total arguments
n = len(sys.argv)




def store(project_id,noOfParagraph,word_list,word_count, date,freq_mapping):
    try:
     
        mySql_insert_query = "INSERT INTO `project_file_detail` (`project_id`, `no_of_paragraph`, `sentence_count`, `word_count`, `created_at`, `distinct_word_count`) VALUES ({},{},{},{},'{}',{} )".format(1,3,len(line_list),len(word_list),'2020-01-01',len(freq_mapping) )
       
        cursor = connection.cursor()
        cursor.execute(mySql_insert_query)
        connection.commit()
        # print(cursor.rowcount, "  inserted successfully  ")
        cursor.close()

    except mysql.connector.Error as error:
        print("Failed to insert {}".format(error))
def store2(project_id,paragraph,content):
    try:
     
        mySql_insert_query = "INSERT INTO `file_content` (`project_id`, `paragraph`, `content`) VALUES ({},{},'{}')".format(project_id,paragraph,content)
       
        cursor = connection.cursor()
        cursor.execute(mySql_insert_query)
        connection.commit()
        # print(cursor.rowcount, "  inserted successfully  ")
        cursor.close()

    except mysql.connector.Error as error:
        print("Failed to insert {}".format(error))


def getText(filename):
    try:
        
        write_file=open((sys.argv[1])+".txt","a+")
        doc = docx.Document(filename)
        fullText = []
        para_count=0;
        project_id=1
        for para in doc.paragraphs:
            para_count+=1
            store2(2,para_count,para.text)

            
            for text in para.text.split('.'):
                write_file.write(text.strip()+"\n")
            fullText.append(para.text)
        return '\n'.join(fullText)
    except Exception as e:
        print("Failed to insert {}".format(e))
def read_file(filename): 
	
	try: 
		with open(filename, 'r') as f: 
			data = f.read() 
		return data 
    
	except IOError: 
		print("Error opening or reading input file: ", filename) 
		sys.exit() 

# splitting the text lines into words 
# translation table is a global variable 
# mapping upper case to lower case and 
# punctuation to spaces 
translation_table = str.maketrans(string.punctuation+string.ascii_uppercase, 
									" "*len(string.punctuation)+string.ascii_lowercase) 
	
# returns a list of the words 
# in the file 
def get_words_from_line_list(text): 
	
	text = text.translate(translation_table) 
	word_list = text.split() 
	
	return word_list 


# counts frequency of each word 
# returns a dictionary which maps 
# the words to their frequency. 
def count_frequency(word_list): 
	
	D = {} 
	
	for new_word in word_list: 
		
		if new_word in D: 
			D[new_word] = D[new_word] + 1
			
		else: 
			D[new_word] = 1
			
	return D 

# returns dictionary of (word, frequency) 
# pairs from the previous dictionary. 
def word_frequencies_for_file(filename): 
	
    line_list = getText(filename) 
    word_list = get_words_from_line_list(line_list) 
    freq_mapping = count_frequency(word_list)

    # print("File", filename, ":", ) 
    # print(len(line_list), "lines, ", ) 
    # print(len(word_list), "words, ", ) 
    # print(len(freq_mapping), "distinct words")
    # sql="INSERT INTO `project_file_detail` (`project_id`, `no_of_paragraph`, `sentence_count`, `word_count`, `created_at`, `distinct_word_count`) VALUES ({},{},{},{},'{}',{} )".format(1,3,len(line_list),len(word_list),'2020-01-01',len(freq_mapping) )
    # print(sql)
    print("{},{},{}".format(len(line_list),len(word_list),len(freq_mapping)))

# returns the angle in radians 
# between document vectors 
def documentSimilarity(filename_1): 
    sorted_word_list_1 = word_frequencies_for_file(filename_1) 
    # return sorted_word_list_1


	
# Driver code 

file1=sys.argv[1]

res=documentSimilarity(file1)
# print(res)
