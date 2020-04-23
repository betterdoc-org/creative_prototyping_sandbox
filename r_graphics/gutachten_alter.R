library(plotly)

x <- c("0-9", "10-19", "20-29", "30-39", "40-49", "50-59", "60-69", "70-79", "80+")
y <- c(0, 11, 22, 22, 0, 22, 22, 0, 0)
data <- data.frame(x, y)

fig <- plot_ly(data, x = ~x, y = ~y, type = 'bar',
               text = y, textposition = 'auto',
               marker = list(color = c('#EDFFE6'), 
                             line = list(color = '#267B00', width = 0.7),
                             width= 500
               ))
fig <- fig %>% layout(title = "Gutachten je Altersgruppe",
                      xaxis = list(title = "Altersgruppe"),
                      yaxis = list(title = "An Patienten versendete Gutachten (%)"),
                      paper_bgcolor='#f5f5f5',
                      plot_bgcolor='#f5f5f5',
                      margin='15px',
                      images = list(
                        list(source = "https://www.betterdoc.org/assets/gfx/logo_square-f39fedc6477536ae4f318c10220953414c18560c6bdb82a00c9ba3937ce735ed.png",
                             xref = "paper",
                             yref = "paper",
                             x= 1,
                             y= 1.2,
                             sizex = 1,
                             sizey = 1,
                             opacity = 0.8
                        )
                      ))

fig
