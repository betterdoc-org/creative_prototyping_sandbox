library(plotly)

x <- c('Rücken', 'Schulter', 'Hüfte', 'Knie', 'Fuss', 'Hand')
y <- c(2, 1, 2, 1, 1, 2)
data <- data.frame(x, y)

fig <- plot_ly(data, x = ~x, y = ~y, type = 'bar',
               text = y, textposition = 'auto',
               marker = list(color = c('#EDFFE6'), 
                             line = list(color = '#267B00', width = 0.7),
                             width= 500
                             ))
fig <- fig %>% layout(title = "Gutachten je Indikationsbereich",
                      xaxis = list(title = "Indikation"),
                      yaxis = list(title = "An Patienten versendete Gutachten", scaleanchor = 1),
                      paper_bgcolor='#f5f5f5',
                      plot_bgcolor='#f5f5f5',
                      margin='15px')

fig



