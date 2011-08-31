'''
Created on Dec 29, 2010
@author: Wesley
'''
import re
from AdditionalFuncs import *

def getListInfo(source,id):
        '''@param source: source code of the page to be parsed
        Returns a list with the following elements [
        'College ID', 'College Name', 'City', 'State', 'Instate Tuition', 'Out of ST Tuition',
        'Room & Board', '% Need Filled','Gift Aid%', 'Loans/Jobs'] '''
        #reg = re.compile('College Search - (?P<name>[\w\s]+)\s-.*?<div id="profile">.*?</h1>.*?(?P<city>[\w\s]+),&nbsp.*?(?P<state>[\w\s]+).*?In-state tuition and fees.*?<strong>(?P<instate>[^<]+).*?Out-of-state tuition and fees.*?<strong>(?P<outstate>[^<]+).*?Room and board.*?<td >(?P<board>[^<]+).*?Average percent of need met: (?P<needMet>[^<]+).*?Scholarships / grants: (?P<grant>[^<]+).*?Loans / jobs: (?P<loans>[^<]+)',re.DOTALL)
        testRE=re.compile('<h3>Annual College Costs.*?</h3>',re.DOTALL)
        testM = testRE.search(source)
        if testM and "(" in testM.group(0):
            reg = re.compile('.*html xmlns.*College Search - (?P<name>[\w\s]+)\s-.*?<div id="profile">.*?</h1>.*?(?P<city>\w[\w\s]*\w),&nbsp.*?(?P<state>\w[\w\s]*\w).*profileId=9">International Students.*<div class="profile_detail" id="costfinaid">.*Annual College Costs [^<].*<th scope="row" ><strong>In-state tuition and fees.*?<strong>(?P<instate>[^<]+).*?Out-of-state tuition and fees.*?<strong>(?P<outstate>[^<]+).*?Room and board.*?<td >(?P<board>[^<]+).*?Average percent of need met: (?P<needMet>[^<]+).*?Scholarships / grants: (?P<grant>[^<]+).*?Loans / jobs: (?P<loans>[^<]+)',re.DOTALL)
            matched= reg.match(source)
            if matched:
                return [id,matched.group('name'),matched.group('city'),matched.group('state'),matched.group('instate'),matched.group('outstate'),matched.group('board'),matched.group('needMet'),matched.group('grant'),matched.group('loans')]
            else:
                return [id]
        else: return [id]
        
def getURLlist(minNum,maxNum):
    '''@param minNum: lowest collegeID value to start at,
    @param maxNum: highest collegeID value to end with
    returns a list of all URLS for all colleges in CB database'''
    urlStart = "http://collegesearch.collegeboard.com/search/CollegeDetail.jsp?collegeId="
    urlEnd="&profileId=2"
    list=[]
    for x in range(minNum,maxNum+1):
        list.append(urlStart+str(x)+urlEnd)
    return list

def getRows(urlList):
    '''@param sourceList: list of URLS to be parsed for row information
    returns a list with rows for all information needed to be in file '''
    rowList=[]
    titleRow=['College ID', 'School', 'City', 'State', 'Instate Tuition', 'Out of ST Tuition','Room & Board', '% Need Filled','Gift Aid%', 'Loans/Jobs']
    rowList.append(titleRow)
    print rowList
    count=1
    for url in urlList:
        print count
        rowList.append(getListInfo(getPageSource(url),count))
        count=count+1
    return rowList
'''
url="http://collegesearch.collegeboard.com/search/CollegeDetail.jsp?collegeId=1464&profileId=2"
test = getListInfo(getPageSource(url),1)
print str(test)
toCSV(getRows(getURLlist(1,10)),'test2.csv')
'''

'''
url="http://collegesearch.collegeboard.com/search/CollegeDetail.jsp?collegeId=2245&profileId=2"
test = getListInfo(getPageSource(url),1)
print str(test)
'''
