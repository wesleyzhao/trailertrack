'''
Created on Dec 29, 2010
@author: Wesley
'''
import urllib2,csv

def getPageSource(url):
    '''@param url: string of url to be passed and to find source
    Takes a string URL and returns a string of the source code '''
    page = urllib2.urlopen(url)
    source = page.read()
    return source

def toCSV(list,path):
    '''@param list: list of list objects that represent rows to be written
    @param name: string path of the file to be written to
    takes in a list of lists (that represent rows) and writes them to a csv file in order '''
    '''example: 
    row1=['a1','a2','a3'], row2=['b1','b2','b3'], row3=['c1','c2','c3'], path='folder/name.csv'
    toCSV([row1,row2,row3],path) will write the rows to each row in the csv file'''
    file = open(path,'wb')
    csvWrit = csv.writer(file)
    csvWrit.writerows(list)
    file.close()
    