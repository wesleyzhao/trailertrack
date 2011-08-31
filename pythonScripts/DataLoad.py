'''
Created on Dec 25, 2010
@author: Wesley
'''
from PageReader import *
import MySQLdb
import urllib2, re, string

def populateMovies(url):
    source = readPage(url)
    movDict = scrapeInfo(getTrailerInfo(source))
    dexMap = indexMap(movDict)
    imdbDict=scrapeIMDB(dexMap.keys())
    def joinCommas(index,key):
        return string.join(imdbDict[index][key],',')
    for index in dexMap:  
        cursor=connect()
        movieDict = movDict[dexMap[index]]
        releaseStr=string.join([(imdbDict[index]['Released'])[keys] for keys in imdbDict[index]['Released'].keys()],',')
        trueSrc=dexMap[index]+'_'+movDict[dexMap[index]]['ClipType']+'.mov' 
        ##need to change INSERT IGNORE to better handling
        testCMD = "SELECT %s FROM %s WHERE %s='%s'"%('TrailerSrc','movieInfo','movieTitle',dexMap[index])
        cursor.execute(testCMD)
        fetched=cursor.fetchone()
        if fetched:
            prev = fetched[0]
            cursor.execute("UPDATE %s SET %s='%s' WHERE %s='%s'"%('movieInfo','TrailerSrc',prev+','+trueSrc,'movieTitle',dexMap[index]))  
        else:
            cmd="""
            INSERT IGNORE INTO movieInfo(movieTitle,IMDBrefer,PosterSrc,TrailerSrc,Directors,Writers,Actors,Genres,Released)
            VALUES
                ('%s','%s','%s','%s','%s','%s','%s','%s','%s')
            """%(dexMap[index],index,movieDict['PosterSrc'],trueSrc,joinCommas(index,'Directors'),joinCommas(index,'Writers'),joinCommas(index,'Actors'),joinCommas(index,'Genres'),releaseStr)
        
            cursor.execute(cmd)
        print 'pass1'
    clipDict={}
    for key in movDict.keys():
        src = movDict[key]['TrailerSrc']
        type = movDict[key]['ClipType']
        mySrc=key+"_"+type+".mov"
        clipDict[mySrc]=src
        imdbREF=movDict[key]['IMDBrefer']
        cursor=connect()
        ##need to change INSERT IGNORE to better handling
        cursor.execute("""
        INSERT IGNORE INTO trailerInfo(TrailerSrc,ClipType, IMDBrefer)
        VALUES
            ('%s','%s','%s')
        """%(mySrc,type,imdbREF))
        
        '''block of 0.1'''
        ##imdbPut('Actors','Actor',joinComma(imdbDict[imdbREF]['Actors']),imdbREF)
        ##imdbPut('Directors','Director',joinComma(imdbDict[imdbREF]['Directors']),imdbREF)
        ##imdbPut('Genres','Genre',joinComma(imdbDict[imdbREF]['Genres']),imdbREF)
        
        imdbPut('Actors','Actor',imdbDict[imdbREF]['Actors'],imdbREF)
        imdbPut('Directors','Director',imdbDict[imdbREF]['Directors'],imdbREF)
        imdbPut('Genres','Genre',imdbDict[imdbREF]['Genres'],imdbREF)
        print 'good2go'
    ##getClipsDict('',clipDict) 

''' block of 0.1       
def imdbPut(table,keyName,keyValue,IMDBrefer):
    cursor=connect()
    addIf(table,keyName,keyValue,'IMDBrefer',IMDBrefer)
'''
def imdbPut(table,keyName,keyList,IMDBrefer):
    '''Takes in the @table to be modified, the @keyName (either Actors, Directors, or Genres, or anything with an
    IMDBrefer as as rowName. Then inserts all the IMDBrefer to the values of the @keyList'''
    for keyVal in keyList:
        cursor=connect()
        addIf(table,keyName,keyVal,'IMDBrefer',IMDBrefer)  
    
def connect():
    conn = MySQLdb.connect(host="173.201.88.44",user="trailtrackmov",passwd="pwn463PWN",db="trailtrackmov")
    return conn.cursor()
        
def addIf(table,keyName,keyValue,rowName,rowValue):
    '''Checks the value of @keyValue based off the @keyName from the @table
    If there was an @IMDBrefer value then replace it with a comma and the new IMDBrefer
    If there is not, then simply add the @keyValue to the @keyName, and the @IMDBrefer'''
    cursor = connect()
    cursor.execute("SELECT %s FROM %s WHERE %s='%s'"%(rowName,table,keyName,keyValue))
    fetched = cursor.fetchone()
    if fetched:
        '''block from v0.1
        for x in fetched:
            tu=x
        '''
        tu=fetched[0]
        cursor.execute("UPDATE %s SET %s='%s' WHERE %s='%s'"%(table,rowName,tu+','+rowValue,keyName,keyValue))  
    else:
        cursor.execute("INSERT INTO %s (%s,%s) VALUES ('%s','%s')"%(table,rowName,keyName,rowValue,keyValue))
        
def joinComma(list):
    return string.join(list,',')
        
    
populateMovies('http://trailerfreaks.com')