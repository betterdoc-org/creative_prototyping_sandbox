City<-c("X","Y","Z","X","Z","X","Y")
House_Unit_Id<-c("H1","H2","H3","H4","H5","H6","H7")
Adult<-c(50,100,60,40,50,80,60)
Child<-c(40,0,40,20,50,20,30)
Baby<-c(10,0,0,40,0,0,10)
data<-data.frame(City,House_Unit_Id,Adult,Child,Baby)

library(plyr)
# Changing the data frame before plotting ... there is propably an easier way to do this!
newdata <- ldply(3:5,function(n){tempdata <- data[,c(1,n)]
colnames(tempdata)[2] <- "Number"
tempdata$type <- colnames(data[n])
return(tempdata)})
newdata <- ddply(newdata,.(City,type),summarize,Number=sum(Number))
# Total for each city
datatotal <- ddply(newdata,~City,summarize,n=sum(Number))
# Merge the data frames together
newdata <- merge(newdata,datatotal)
# Calc the percentages
newdata$perc <- newdata$Number/newdata$n

plot_ly(newdata,x = ~City, y = ~perc*100, type = 'bar',color = ~type,text=~Number,hoverinfo = 'text') %>% 
  layout(yaxis = list(title = 'Percentage (%)'),barmode = "stack") 